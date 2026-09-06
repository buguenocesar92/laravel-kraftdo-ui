<?php

/**
 * CCTG-27/28/29 — un hook que solo vive en .git/hooks no viaja con el clon.
 *
 * Estos candados leen el disco real: no simulan un commit ni un push (eso
 * necesitaría un repositorio git de prueba), pero garantizan que la próxima
 * persona que clone el repo y corra `composer install` recibe los tres hooks
 * activados, y que cada uno sigue teniendo el contenido que le toca.
 */
it('composer.json activa core.hooksPath al instalar y actualizar', function () {
    $composer = json_decode(
        (string) file_get_contents(__DIR__.'/../../composer.json'),
        associative: true,
        flags: JSON_THROW_ON_ERROR,
    );

    $scripts = $composer['scripts'] ?? [];

    foreach (['post-install-cmd', 'post-update-cmd'] as $evento) {
        $comandos = implode(' ', (array) ($scripts[$evento] ?? []));
        expect($comandos)->toContain('core.hooksPath')
            ->and($comandos)->toContain('.githooks');
    }
});

it('los tres hooks de git están versionados y son ejecutables', function () {
    foreach (['pre-commit', 'pre-push', 'commit-msg'] as $hook) {
        $ruta = __DIR__.'/../../.githooks/'.$hook;

        expect(is_file($ruta))->toBeTrue("falta .githooks/{$hook}")
            ->and(is_executable($ruta))->toBeTrue("«.githooks/{$hook}» no es ejecutable");
    }
});

it('pre-push rechaza el envío directo a main/master sin override por variable', function () {
    $prePush = (string) file_get_contents(__DIR__.'/../../.githooks/pre-push');

    expect($prePush)->toContain('refs/heads/main')
        ->and($prePush)->toContain('refs/heads/master')
        // El corte a main es incondicional: a diferencia de la suite (que sí
        // se puede saltar con SKIP_PREPUSH_TESTS), esta rama no tiene variable
        // de entorno que la desactive — solo --no-verify, que queda en el log.
        ->and($prePush)->not->toContain('SKIP_PREPUSH_MAIN');
});

it('pre-push corre la suite antes de publicar a develop o main', function () {
    $prePush = (string) file_get_contents(__DIR__.'/../../.githooks/pre-push');

    expect($prePush)->toContain('vendor/bin/pest')
        ->and($prePush)->toContain('SKIP_PREPUSH_TESTS');
});

it('commit-msg rechaza la atribución a una IA', function () {
    $commitMsg = (string) file_get_contents(__DIR__.'/../../.githooks/commit-msg');

    expect($commitMsg)->toContain('co-authored-by')
        ->and($commitMsg)->toContain('anthropic');
});

it('pre-commit escanea secretos con gitleaks y formatea lo staged con Pint', function () {
    $preCommit = (string) file_get_contents(__DIR__.'/../../.githooks/pre-commit');

    expect($preCommit)->toContain('gitleaks')
        ->and($preCommit)->toContain('vendor/bin/pint');
});

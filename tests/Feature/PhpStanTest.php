<?php

/**
 * CCTG-15 — PHPStan nivel 8 sin baseline, bloqueante en CI y en pre-commit.
 *
 * No corre PHPStan aquí dentro (sería un proceso Pest lanzando otro proceso
 * pesado en cada `pest` local); en cambio fija que la configuración exista,
 * apunte a nivel 8 sin baseline y sin ignoreErrors masivos, y que CI y el
 * pre-commit lo ejecuten de verdad.
 */
it('phpstan.neon exige nivel 8 sin baseline ni ignoreErrors masivos', function () {
    $config = (string) file_get_contents(__DIR__.'/../../phpstan.neon');

    expect($config)->toMatch('/level:\s*8\b/')
        ->and($config)->not->toContain('baseline')
        ->and($config)->toContain('paths')
        ->and($config)->toContain('src');

    // "ignoreErrors" puntual y documentado se tolera; una lista larga sin
    // comentario que explique cada excepción es un baseline disfrazado.
    preg_match_all('/^\s*-\s*message:/m', $config, $ignoresPuntuales);
    expect(count($ignoresPuntuales[0]))->toBeLessThanOrEqual(2);
});

it('larastan está en require-dev', function () {
    $composer = json_decode(
        (string) file_get_contents(__DIR__.'/../../composer.json'),
        associative: true,
        flags: JSON_THROW_ON_ERROR,
    );

    expect($composer['require-dev'] ?? [])->toHaveKey('larastan/larastan');
});

it('CI corre PHPStan sin --generate-baseline', function () {
    $ci = ciYml();

    expect($ci)->toContain('phpstan')
        ->and($ci)->not->toContain('--generate-baseline');
});

it('pre-commit analiza con PHPStan los .php staged', function () {
    $preCommit = (string) file_get_contents(__DIR__.'/../../.githooks/pre-commit');

    expect($preCommit)->toContain('phpstan')
        ->and($preCommit)->toContain('memory-limit=1G');
});

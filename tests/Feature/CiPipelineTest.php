<?php

/**
 * Candados sobre el propio .github/workflows/ci.yml: no se puede ejecutar el
 * workflow desde la suite (correría en GitHub Actions), pero si el paso o el
 * job desaparecen del YAML, esto tiene que fallar.
 */
function ciYml(): string
{
    return (string) file_get_contents(__DIR__.'/../../.github/workflows/ci.yml');
}

it('el .gitleaks.toml existe y extiende las reglas por defecto', function () {
    $config = (string) file_get_contents(__DIR__.'/../../.gitleaks.toml');

    expect($config)->toContain('useDefault = true');
});

it('CI escanea secretos en el árbol y en todo el historial con gitleaks', function () {
    // CCTG-19: un .env commiteado y luego borrado en el commit siguiente sigue
    // en el historial. `gitleaks dir` solo mira el árbol de trabajo actual;
    // `gitleaks git` es el que revisa los commits anteriores, y necesita el
    // historial completo (fetch-depth: 0) para poder verlo.
    $ci = ciYml();

    expect($ci)->toContain('fetch-depth: 0')
        ->and($ci)->toContain('gitleaks dir')
        ->and($ci)->toContain('gitleaks git')
        ->and($ci)->toContain('.gitleaks.toml')
        // El binario va fijado a una versión concreta, no al action flotante
        // (que además pide licencia para uso en organizaciones).
        ->and($ci)->toMatch('/VERSION:\s*[\d.]+/');
});

it('CI corre composer audit --no-dev sin continue-on-error que lo absorba', function () {
    // CCTG-18: una dependencia con CVE conocido no puede entrar a main sin que
    // nadie lo vea. El paso ya no termina en `|| true` ni con
    // continue-on-error a nivel de step.
    $ci = ciYml();

    expect($ci)->toContain('composer audit')
        ->and($ci)->toContain('--no-dev')
        ->and($ci)->not->toContain('composer audit --no-dev || true')
        ->and($ci)->not->toContain('composer audit --no-dev  || true');
});

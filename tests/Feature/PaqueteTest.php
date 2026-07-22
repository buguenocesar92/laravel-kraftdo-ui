<?php

use Illuminate\Support\Facades\Blade;

it('registra los componentes bajo el namespace kd', function () {
    $html = Blade::render('<x-kd::badge>Hola</x-kd::badge>');

    expect($html)->toContain('Hola');
});

it('define los tokens de marca con los valores de la landing', function () {
    $css = file_get_contents(__DIR__.'/../../resources/css/kraftdo-ui.css');

    expect($css)->toContain('--kd-green: #10b981')
        ->and($css)->toContain('--kd-lime: #32ff32')
        ->and($css)->toContain('--kd-blue: #3b82f6');
});

it('los componentes no usan tokens de marca directamente', function () {
    // Deben pasar por los semánticos (--kd-accent), o un cambio de paleta
    // obligaría a editar los 48 componentes.
    $encontrados = [];
    foreach (glob(__DIR__.'/../../resources/views/components/*.blade.php') as $f) {
        if (preg_match('/var\(--kd-(green|lime|blue|navy|dark)\b/', (string) file_get_contents($f))) {
            $encontrados[] = basename($f);
        }
    }

    expect($encontrados)->toBe([]);
});

it('no quedan rastros de la identidad municipal', function () {
    $restos = [];
    foreach (glob(__DIR__.'/../../resources/views/components/*.blade.php') as $f) {
        if (preg_match('/--muni-|x-muni::|gob-escudo/', (string) file_get_contents($f))) {
            $restos[] = basename($f);
        }
    }

    expect($restos)->toBe([]);
});

it('ningún par texto/fondo baja de WCAG AA', function () {
    // Este test existe porque el spec fijaba --kd-on-accent en blanco, que sobre
    // el verde de marca da 2.54:1. Nadie lo calculó hasta que un revisor lo hizo
    // a mano. A partir de aquí lo calcula CI.
    $css = file_get_contents(__DIR__.'/../../resources/css/kraftdo-ui.css');

    $pares = [['--kd-text', '--kd-bg'], ['--kd-muted', '--kd-bg'], ['--kd-on-accent', '--kd-accent'], ['--kd-on-danger', '--kd-danger-fg']];
    $malos = [];

    foreach ([':root', '.dark'] as $tema) {
        foreach ($pares as [$fg, $bg]) {
            $ratio = kdContraste(kdToken($css, $tema, $fg), kdToken($css, $tema, $bg));
            if ($ratio < 4.5) {
                $malos[] = sprintf('%s: %s sobre %s = %.2f:1', $tema, $fg, $bg, $ratio);
            }
        }
    }

    expect($malos)->toBe([]);
});

it('las animaciones se anulan con prefers-reduced-motion', function () {
    $css = file_get_contents(__DIR__.'/../../resources/css/kraftdo-animations.css');

    expect($css)->toContain('prefers-reduced-motion: reduce')
        ->and($css)->toContain('animation: none');
});

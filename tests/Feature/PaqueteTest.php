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
    //
    // Los 3 temas reales son :root, .dark y [data-kd-theme="light"] (el tercero
    // congela el claro aunque el SO o Filament pidan oscuro). Si alguno de los
    // dos bloques "light" se desincroniza, este test tiene que notarlo.
    $css = file_get_contents(__DIR__.'/../../resources/css/kraftdo-ui.css');

    $pares = [['--kd-text', '--kd-bg'], ['--kd-muted', '--kd-bg'], ['--kd-on-accent', '--kd-accent'], ['--kd-on-danger', '--kd-danger-fg']];
    $malos = [];

    foreach ([':root', '.dark', '[data-kd-theme="light"]'] as $tema) {
        foreach ($pares as [$fg, $bg]) {
            $ratio = kdContraste(kdToken($css, $tema, $fg), kdToken($css, $tema, $bg));
            if ($ratio < 4.5) {
                $malos[] = sprintf('%s: %s sobre %s = %.2f:1', $tema, $fg, $bg, $ratio);
            }
        }
    }

    expect($malos)->toBe([]);
});

it('ningún token usado como color de texto en los componentes baja de WCAG AA', function () {
    // El test anterior solo cubre 4 pares fijos, elegidos a mano. Pero los
    // componentes usan MÁS tokens como `color:` (texto) de los que ese test
    // conoce: --kd-accent, --kd-hint, --kd-warn-fg, --kd-danger-fg, --kd-info-fg,
    // --kd-accent-strong. Ninguno de esos pares estaba cubierto, y varios no
    // llegan a 4.5:1 contra el fondo de página.
    //
    // En vez de escribir la lista de tokens a mano (se desactualiza en el
    // próximo componente que use un token nuevo como texto), se deriva del
    // propio código fuente con una expresión regular sobre la propiedad
    // `color:`. Así el test se entera solo de un token nuevo sin que nadie
    // tenga que acordarse de tocarlo.
    $cssTokens = file_get_contents(__DIR__.'/../../resources/css/kraftdo-ui.css');

    // Los usos reales de tokens como texto viven en los 48 componentes Blade y
    // en el CSS del plugin de Filament (los estilos del sidebar/heading usan
    // `color:` inline, no pasan por Blade).
    $fuentes = array_merge(
        glob(__DIR__.'/../../resources/views/components/*.blade.php'),
        [__DIR__.'/../../resources/css/kraftdo-ui-filament.css']
    );

    $tokens = [];
    foreach ($fuentes as $f) {
        $contenido = (string) file_get_contents($f);
        // El lookbehind negativo excluye `border-color:`, `background-color:`,
        // `border-left-color:`, etc. — esos no son texto, son relleno o borde,
        // y no tienen que cumplir contraste de texto (4.5:1). Solo la propiedad
        // `color` desnuda pinta letras.
        if (preg_match_all('/(?<![a-zA-Z-])color:\s*var\(\s*(--kd-[a-z-]+)\s*\)/', $contenido, $m)) {
            foreach ($m[1] as $token) {
                $tokens[$token] = true;
            }
        }
    }

    // --kd-on-accent y --kd-on-danger son texto para un RELLENO sólido
    // específico (el botón primario en verde, el botón de peligro en hover),
    // no para el fondo de página — y ya los cubre el test anterior contra su
    // propio fondo. Medirlos aquí contra --kd-bg/--kd-surface sería una
    // comparación que nunca ocurre en pantalla (en dark, --kd-on-accent vale
    // igual que --kd-bg: 1:1 consigo mismo, un falso positivo).
    unset($tokens['--kd-on-accent'], $tokens['--kd-on-danger']);

    // --kd-bg se usa una vez como texto (tooltip: patrón invertido, letra clara
    // sobre relleno oscuro que es --kd-text, no --kd-bg/--kd-surface) y
    // --kd-border-2 se usa como relleno de un ícono decorativo (estrella sin
    // marcar), no como texto legible. Ninguno de los dos se renderiza sobre
    // --kd-bg/--kd-surface, así que se excluyen por el mismo motivo de arriba.
    unset($tokens['--kd-bg'], $tokens['--kd-border-2']);

    // Si la extracción se rompe (p. ej. cambia el formato del CSS) y esto queda
    // vacío, el test de abajo pasaría en verde sin medir nada. Mejor fallar
    // ruidosamente.
    expect($tokens)->not->toBeEmpty();

    $malos = [];
    foreach ([':root', '.dark', '[data-kd-theme="light"]'] as $tema) {
        foreach (array_keys($tokens) as $token) {
            foreach (['--kd-bg', '--kd-surface'] as $fondo) {
                $ratio = kdContraste(kdToken($cssTokens, $tema, $token), kdToken($cssTokens, $tema, $fondo));
                if ($ratio < 4.5) {
                    $malos[] = sprintf('%s: %s sobre %s = %.2f:1', $tema, $token, $fondo, $ratio);
                }
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

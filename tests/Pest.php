<?php

use Kraftdo\Ui\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

/** Ratio de contraste WCAG 2.1 entre dos colores hexadecimales. */
function kdContraste(string $a, string $b): float
{
    $lum = function (string $hex): float {
        $canales = array_map(fn (string $p): float => hexdec($p) / 255, str_split(ltrim($hex, '#'), 2));
        $canales = array_map(
            fn (float $x): float => $x <= 0.03928 ? $x / 12.92 : (($x + 0.055) / 1.055) ** 2.4,
            $canales
        );

        return 0.2126 * $canales[0] + 0.7152 * $canales[1] + 0.0722 * $canales[2];
    };

    $la = $lum($a);
    $lb = $lum($b);

    return (max($la, $lb) + 0.05) / (min($la, $lb) + 0.05);
}

/**
 * Valor hex de un token dentro de un bloque de tema, resolviendo la indirección
 * `var(--otro)` contra `:root`.
 *
 * Falla ruidosamente si el bloque o el token no existen. Un fallback silencioso
 * haría que el test midiera el tema equivocado y pasara en verde — que es
 * exactamente lo que pasó en la primera versión de este helper.
 */
function kdToken(string $css, string $selector, string $token): string
{
    // El selector puede aparecer en VARIOS bloques (p. ej. hay dos `:root {`: uno
    // solo de marca y otro semántico). Se fusionan todos sus cuerpos antes de
    // buscar el token; leer solo el primero mediría el bloque equivocado sin
    // lanzar. Un bloque más abajo gana, como en la cascada CSS.
    $bloques = '';
    $desde = 0;
    while (($pos = strpos($css, $selector.' {', $desde)) !== false) {
        $abre = strpos($css, '{', $pos);
        $cierra = strpos($css, '}', $abre);
        $bloques = substr($css, $abre, $cierra - $abre)."\n".$bloques;
        $desde = $cierra;
    }
    if ($bloques === '') {
        throw new RuntimeException("No existe el bloque «{$selector}» en el CSS.");
    }

    // Resuelve el valor de un token; si es `var(--otro)`, sigue la indirección
    // contra todo el CSS (los tokens destino viven en algún `:root`).
    $leer = function (string $donde, string $t) use (&$leer, $css) {
        if (preg_match('/'.preg_quote($t, '/').'\s*:\s*([^;]+);/', $donde, $m) !== 1) {
            return null;
        }
        $valor = trim($m[1]);
        if (preg_match('/^var\(\s*(--[\w-]+)\s*\)$/', $valor, $v) === 1) {
            return $leer($css, $v[1]);
        }

        return preg_match('/^#[0-9a-f]{6}$/i', $valor) === 1 ? $valor : null;
    };

    // El token DEBE estar en el bloque del selector pedido: sin fallback global
    // para la búsqueda primaria, o mediría el tema equivocado en silencio.
    $hex = $leer($bloques, $token);
    if ($hex === null) {
        throw new RuntimeException("El token «{$token}» no resuelve a un hex en «{$selector}».");
    }

    return $hex;
}

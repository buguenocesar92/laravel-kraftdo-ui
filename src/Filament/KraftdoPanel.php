<?php

namespace Kraftdo\Ui\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;

/**
 * Viste cualquier panel Filament con la identidad de KraftDo, sin repetir la
 * configuración en cada sistema del ecosistema.
 *
 * Aplica de una sola vez:
 *   - el tema (kraftdo-ui-filament.css),
 *   - el degradado de marca sobre la barra superior,
 *   - la paleta, con el verde KraftDo como primario.
 *
 * Uso en el PanelProvider:
 *   ->plugin(KraftdoPanel::make())
 *
 * El CSS se sirve desde public/vendor/kraftdo-ui/ (publicar con
 * `php artisan vendor:publish --tag=kraftdo-ui-filament`).
 */
class KraftdoPanel implements Plugin
{
    /**
     * Degradado de marca. Va literal y no como `var(--kd-grad-primary)` porque
     * se inyecta en un `style` inline de un panel que puede no haber cargado
     * kraftdo-ui.css. Mismo valor que el token: kd-blue → kd-green.
     */
    private const FRANJA = 'linear-gradient(90deg,#3b82f6,#10b981)';

    private bool $aplicarColores = true;

    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'kraftdo-panel';
    }

    /**
     * Por defecto fija la paleta de marca. Se puede desactivar si un sistema
     * define su propio acento.
     */
    public function conColores(bool $aplicar = true): static
    {
        $this->aplicarColores = $aplicar;

        return $this;
    }

    public function register(Panel $panel): void
    {
        $panel
            // Tema KraftDo: el panel deja de verse genérico.
            ->renderHook(
                PanelsRenderHook::STYLES_AFTER,
                fn (): string => '<link rel="stylesheet" href="'.self::asset('vendor/kraftdo-ui/filament.css').'">',
            )
            // Degradado de marca sobre la barra superior.
            ->renderHook(
                PanelsRenderHook::TOPBAR_BEFORE,
                fn (): string => '<div role="presentation" aria-hidden="true" style="height:4px;background:'.self::FRANJA.'"></div>',
            );

        if ($this->aplicarColores) {
            $panel->colors([
                'primary' => Color::generateV3Palette('#10b981'),  // kd-green
                'info' => Color::generateV3Palette('#3b82f6'),     // kd-blue
                'success' => Color::generateV3Palette('#10b981'),
                'warning' => Color::generateV3Palette('#f59e0b'),
                'danger' => Color::generateV3Palette('#ef4444'),
                'gray' => Color::generateV3Palette('#1e293b'),     // kd-navy
            ]);
        }
    }

    public function boot(Panel $panel): void
    {
        //
    }

    /**
     * Usa el helper de assets versionados del proyecto si existe (cache-busting
     * en producción); si no, cae al `asset()` estándar de Laravel.
     */
    private static function asset(string $path): string
    {
        return function_exists('asset_versionado') ? asset_versionado($path) : asset($path);
    }
}

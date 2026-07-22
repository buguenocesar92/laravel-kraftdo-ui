<?php

namespace Kraftdo\Ui;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

/**
 * Registra el sistema de diseño municipal: los componentes Blade anónimos bajo el
 * namespace `muni` (`<x-kd::kpi>`) y publica el CSS de tokens para que la app lo
 * importe en su pipeline de Tailwind v4.
 */
class KraftdoUiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Componentes anónimos bajo <x-kd::...>. Los .blade.php viven en el paquete;
        // la app no necesita publicarlos (se resuelven desde vendor/).
        Blade::anonymousComponentPath(__DIR__.'/../resources/views/components', 'kd');

        // CSS de tokens: la app hace `@import` desde vendor/ (ver README) o lo publica
        // a resources/css para personalizarlo por proyecto.
        $this->publishes([
            __DIR__.'/../resources/css/kraftdo-ui.css' => resource_path('css/vendor/kraftdo-ui.css'),
        ], 'kraftdo-ui-css');

        // Escudo oficial del municipio. `<x-kd::gob-escudo>` lo sirve desde
        // public/vendor/kraftdo-ui/, así que hay que publicarlo en cada sistema:
        //   php artisan vendor:publish --tag=kraftdo-ui-images
        $this->publishes([
            __DIR__.'/../resources/images' => public_path('vendor/kraftdo-ui'),
        ], 'kraftdo-ui-images');

        // Tema Filament municipal (CSS plano) → public/vendor/kraftdo-ui/filament.css.
        // Se inyecta con un render hook para que los paneles no se vean genéricos:
        //   php artisan vendor:publish --tag=kraftdo-ui-filament --force
        $this->publishes([
            __DIR__.'/../resources/css/kraftdo-ui-filament.css' => public_path('vendor/kraftdo-ui/filament.css'),
        ], 'kraftdo-ui-filament');
    }
}

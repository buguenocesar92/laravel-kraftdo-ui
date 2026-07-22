# laravel-kraftdo-ui

Sistema de diseño y componentes Blade del **ecosistema KraftDo**: un contrato de tokens
`--kd-*` con temas claro/oscuro, 48 componentes de panel de datos y producto, animaciones
de marca y un plugin de Filament. Centraliza en un paquete lo que antes se copiaba entre
sistemas.

Lo consumen `kraftdo-nfc-v2`, `kraftdo-sitio`, `kraftdo-crm` y `kraftdo-hub`.

## Requisitos

- PHP 8.3+, Laravel 12 o 13
- Tailwind CSS **v4** (usa `@theme`, `@custom-variant`, `@source`) — opcional, el CSS es
  plano y funciona igual en v3 o sin Tailwind
- Fuente **Inter** self-hosted (recomendado): sin ella, el sistema degrada a `system-ui`

## Instalación

```bash
composer require kraftdo/laravel-kraftdo-ui
```

El repo es privado. En el `composer.json` del proyecto consumidor hay que apuntar al
repositorio VCS de GitHub — ojo, el **vendor de Composer** es `kraftdo` pero el **dueño en
GitHub** es `buguenocesar92`, no son el mismo nombre:

```json
"repositories": {
    "kraftdo-ui": { "type": "vcs", "url": "https://github.com/buguenocesar92/laravel-kraftdo-ui.git" }
}
```

## CSS: los `@import`

En `resources/css/app.css` del proyecto consumidor:

```css
@import "tailwindcss";
@import "../../vendor/kraftdo/laravel-kraftdo-ui/resources/css/kraftdo-ui.css";
@source "../../vendor/kraftdo/laravel-kraftdo-ui/resources/views/**/*.blade.php";
```

`kraftdo-ui.css` ya importa `kraftdo-animations.css` — no hace falta declararlo aparte.
Para apps en Tailwind v4 que además quieran la variante `dark:` y utilidades `bg-kd-*` /
`text-kd-*` / `border-kd-*`, se agrega **después** de los dos imports anteriores:

```css
@import "../../vendor/kraftdo/laravel-kraftdo-ui/resources/css/kraftdo-ui-tailwind.css";
```

Para personalizar los tokens por proyecto, se publica el CSS y se edita la copia:

```bash
php artisan vendor:publish --tag=kraftdo-ui-css   # → resources/css/vendor/kraftdo-ui.css
```

## Plugin de Filament

`Kraftdo\Ui\Filament\KraftdoPanel` viste cualquier panel Filament con la identidad de
KraftDo: inyecta el tema (`kraftdo-ui-filament.css`), el degradado de marca sobre la barra
superior y fija la paleta primaria/info/success/warning/danger/gray. Requiere
`filament/filament` (paquete opcional, va en `require-dev`; instálalo también en la app
que lo consume).

```bash
php artisan vendor:publish --tag=kraftdo-ui-filament
```

```php
// En el PanelProvider:
->plugin(\Kraftdo\Ui\Filament\KraftdoPanel::make())

// Si el sistema define su propio acento y no quiere la paleta de marca:
->plugin(\Kraftdo\Ui\Filament\KraftdoPanel::make()->conColores(false))
```

## Tokens de marca

Valores tomados del CSS de la landing (kraftdo.cl). Los componentes **nunca** usan estos
tokens directamente — solo los semánticos (`--kd-accent`, `--kd-surface`, `--kd-text`…)
para que un cambio de paleta no obligue a tocar los 48 `.blade.php`.

| Token | Valor |
|-------|-------|
| `--kd-green` | `#10b981` |
| `--kd-green-dark` | `#059669` |
| `--kd-lime` | `#32ff32` |
| `--kd-blue` | `#3b82f6` |
| `--kd-blue-dark` | `#2563eb` |
| `--kd-navy` | `#1e293b` |
| `--kd-dark` | `#0f172a` |

## Uso

```blade
<x-kd::app-shell theme="dark" system="Cuentas por Cobrar" subtitle="KraftDo" status="online">
    <div style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:18px;">
        <x-kd::kpi :value="number_format($total, 0, ',', '.')" label="Resultado del filtro" />
        <x-kd::kpi :value="$vencidas" label="Vencidas" tone="danger" />
        <x-kd::kpi :value="$total - $vencidas" label="Al día" tone="ok" />
    </div>

    <x-kd::filter-bar :action="route('cuentas')">
        <x-kd::field label="Buscar"><input name="buscar" value="{{ $filtros['buscar'] ?? '' }}"></x-kd::field>
        <x-kd::field label="Estado">
            <select name="vencida"><option value="">Todas</option><option value="SI">Solo vencidas</option></select>
        </x-kd::field>
    </x-kd::filter-bar>

    <x-kd::data-table :columns="['Cliente', 'RUT', 'Plan', 'Estado']">
        @foreach ($filas as $fila)
            <tr data-kd-row @class(['kd-row--danger' => $fila['vencida'] === 'SI'])>
                <td>{{ $fila['cliente'] }}</td>
                <td class="kd-num">{{ $fila['rut'] }}</td>
                <td>{{ $fila['plan'] }}</td>
                <td><x-kd::badge :tone="$fila['vencida'] === 'SI' ? 'danger' : 'ok'">{{ $fila['vencida'] === 'SI' ? 'Vencida' : 'Al día' }}</x-kd::badge></td>
            </tr>
        @endforeach
    </x-kd::data-table>
</x-kd::app-shell>
```

## Dark mode universal

El sistema activa el tema oscuro con **cualquiera** de estos mecanismos, para convivir con
todo el ecosistema a la vez — no hay que elegir uno:

| Mecanismo | Para qué |
|-----------|----------|
| `<html class="dark">` | **Filament** (su toggle) y Tailwind class-strategy |
| `<html data-kd-theme="dark">` | Nuestro atributo — congela un tema fijo |
| `<html data-theme="dark">` | Convención de otras UI / PWA-SPA |
| `@media (prefers-color-scheme: dark)` | PWA/SPA que sigue el OS (fallback automático) |

**Jerarquía** (de menor a mayor prioridad): light por defecto → dark por preferencia del OS →
activadores de clase/atributo (ganan sobre el OS) → `data-kd-theme` explícito (override final).

- Dentro de un **panel Filament**: no pongas `data-kd-theme` — los `<x-kd::*>` siguen
  automáticamente el toggle `.dark` de Filament.
- Un **sistema con identidad fija**: pon `data-kd-theme="light"` o `"dark"` y queda inmune
  al OS y a un `.dark` de un ancestro.
- Una **PWA que sigue el OS**: no pongas nada — `prefers-color-scheme` decide.

## Componentes

| Componente | Props principales |
|-----------|-------------------|
| `<x-kd::app-shell>` | `theme` (light/dark), `system`, `subtitle`, `status`, `title`, `maxWidth` |
| `<x-kd::topbar>` | `system`, `subtitle`, `status` (online/degraded/offline), `logo` (slot) |
| `<x-kd::page-header>` | `title`, `subtitle`, `eyebrow`; slot `actions` |
| `<x-kd::stat>` | `value`, `label`, `tone`, `delta`, `deltaDir` (up/down), `spark` (array→sparkline), `hint` |
| `<x-kd::kpi>` | `value`, `label`, `tone` (neutral/ok/warn/danger/info), `hint` |
| `<x-kd::badge>` | `tone`, `dot` |
| `<x-kd::alert>` | `tone` (ok/warn/danger/info), `title`, `icon` (slot HTML) |
| `<x-kd::card>` | `title`, `subtitle`, `flush`; slot `actions` |
| `<x-kd::button>` | `variant` (primary/ghost/subtle/danger), `size` (sm/md/lg), `href`, `icon`, `type` |
| `<x-kd::segmented>` | `name`, `options` (array), `value` — radios reales sin JS; o slot |
| `<x-kd::filter-bar>` | `action`, `method`; slots `submitLabel`, `actions` |
| `<x-kd::field>` | `label`; el control (input/select) va en el slot |
| `<x-kd::data-table>` | `columns` (array), `empty`; el slot son los `<tr data-kd-row>` |
| `<x-kd::pagination>` | `current`, `total`, `url` (closure fn(\$p)), `info` |

### Interactivos (requieren Alpine 3)

| Componente | Props principales |
|-----------|-------------------|
| `<x-kd::dropdown>` | `align` (start/end), `width`; slot `trigger` + ítems `<x-kd::dropdown-item>` |
| `<x-kd::dropdown-item>` | `href`, `icon`, `tone` (default/danger) |
| `<x-kd::modal>` | `title`, `maxWidth`; slots `trigger`, `footer` |
| `<x-kd::tabs>` | `tabs` (array de labels), `default`; paneles `<x-kd::tab-panel :index>` |
| `<x-kd::toast-host>` | `position`; colocar UNA vez. Disparar: `$dispatch('kd-toast', {tone, title, message})` |

```blade
{{-- Modal --}}
<x-kd::modal title="Cancelar la suscripción">
    <x-slot:trigger><x-kd::button variant="danger">Cancelar</x-kd::button></x-slot:trigger>
    Se marcará <b>{{ $cliente->nombre }}</b> como cancelado.
    <x-slot:footer>
        <x-kd::button variant="ghost" x-on:click="open=false">Volver</x-kd::button>
        <x-kd::button x-on:click="open=false; $dispatch('kd-toast',{tone:'ok',message:'Suscripción cancelada'})">Confirmar</x-kd::button>
    </x-slot:footer>
</x-kd::modal>

{{-- Toast: colocar el host una vez, disparar desde cualquier parte --}}
<x-kd::toast-host />
<button x-on:click="$dispatch('kd-toast',{tone:'ok',title:'Guardado',message:'Cambios guardados.'})">Guardar</button>
```

### Formularios, navegación y plantillas de página

| Componente | Props principales |
|-----------|-------------------|
| `<x-kd::input>` | `label`, `name`, `type`, `error`, `hint`, `icon`, `required` |
| `<x-kd::select>` | `label`, `name`, `options`, `selected`, `placeholder`, `error` |
| `<x-kd::switch>` | `label`, `name`, `checked`, `description` (Alpine) |
| `<x-kd::sidebar>` | `width`; slot con `<x-kd::nav-section>` + `<x-kd::nav-item>` (colapsa en móvil) |
| `<x-kd::nav-item>` | `href`, `icon`, `active`, `badge` |
| `<x-kd::nav-section>` | `title` |
| `<x-kd::breadcrumb>` | `items` (array de `['label','url'?]`) |
| `<x-kd::tooltip>` | `text`, `placement` (top/bottom/left/right) (Alpine) |
| `<x-kd::avatar>` | `name` (iniciales), `src`, `size`, `tone` |
| `<x-kd::progress>` | `value`, `max`, `tone`, `label`, `showValue` |
| `<x-kd::skeleton>` | `width`, `height`, `rounded` (shimmer) |
| `<x-kd::empty-state>` | `title`, `description`, `icon`; slot `actions` |
| `<x-kd::command-palette>` | `items`, `placeholder`, `hotkey` — ⌘K/Ctrl+K (Alpine) |
| `<x-kd::auth-shell>` | `theme`, `title`, `system`, `subtitle`, `logo`; slots `aside`, `head` — layout login/registro |
| `<x-kd::dashboard-shell>` | `theme`, `system`, `subtitle`, `status`, `user`; slots `sidebar`, `topbar` — layout de panel |
| `<x-kd::error-page>` | `code`, `title`, `message`, `home`, `theme` — 403/404/500/503 |

Todos respetan `prefers-reduced-motion`, tienen estados `:focus-visible` con anillo de foco
accesible (`--kd-ring`), y micro-interacciones de hover/active con transiciones tokenizadas.
Los interactivos usan **Alpine 3 core** (sin plugins): en los sistemas con Filament ya viene
incluido; en apps sin Filament, `npm i alpinejs` y `Alpine.start()`. El CSS del paquete trae
la regla `[x-cloak]` para evitar el flash inicial.

## Desarrollo

```bash
composer install
vendor/bin/pest
vendor/bin/pint
```

La suite (`tests/Feature/PaqueteTest.php`) verifica el registro del namespace `kd`, los
valores de la paleta de marca, que ningún componente use tokens de marca directamente, que
no queden restos de un origen anterior, y que las animaciones respeten
`prefers-reduced-motion`. Corre en CI (`.github/workflows/ci.yml`) en cada push a
`develop`/`main` y en cada PR.

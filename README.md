# laravel-kraftdo-ui

Sistema de diseño y componentes Blade del **ecosistema municipal de Graneros**.
Centraliza en un paquete lo que hoy está copiado entre sistemas (`feria.css`, `discapacidad.css`):
un contrato de tokens `--kd-*` con dos temas seleccionables y un set de componentes de
panel de datos (topbar, KPIs, tabla densa, filtros, badges).

## Filosofía: maquinaria + presets

El paquete NO impone un look. Empaqueta la **maquinaria** (tokens, componentes) y trae dos
**presets** de arranque para sistemas nuevos:

| Tema | Linaje | Tipografía | Uso |
|------|--------|-----------|-----|
| `light` | discapacidad (`.om`) | DM Sans / DM Mono | institucional sereno, teal |
| `dark` | feria (`.fc`) | IBM Plex Sans / Mono | terminal de datos, alto contraste |

Un sistema existente puede **sobreescribir los tokens** (`--kd-*`) para conservar su
identidad propia sin tocar los componentes.

## Dark mode universal

El sistema activa el tema oscuro con **cualquiera** de estos mecanismos, para convivir con
todo el ecosistema a la vez — no hay que elegir uno:

| Mecanismo | Para qué |
|-----------|----------|
| `<html class="dark">` | **Filament** (su toggle) y Tailwind class-strategy |
| `<html data-muni-theme="dark">` | Nuestro atributo — congela un tema fijo (feria/disc) |
| `<html data-theme="dark">` | Convención de otras UI / PWA-SPA |
| `@media (prefers-color-scheme: dark)` | PWA/SPA que sigue el OS (fallback automático) |

**Jerarquía** (de menor a mayor prioridad): light por defecto → dark por preferencia del OS →
activadores de clase/atributo (ganan sobre el OS) → `data-muni-theme` explícito (override final).

- Dentro de un **panel Filament**: no pongas `data-muni-theme` — los `<x-kd::*>` siguen
  automáticamente el toggle `.dark` de Filament.
- Un **sistema con identidad fija** (discapacidad siempre claro): pon `data-muni-theme="light"`
  y queda inmune al OS y a un `.dark` de un ancestro.
- Una **PWA que sigue el OS**: no pongas nada — `prefers-color-scheme` decide.

## Requisitos

- PHP 8.3+, Laravel 12 o 13
- Tailwind CSS **v4** (usa `@theme`, `@custom-variant`, `@source`)
- Fuentes self-hosted (recomendado): `@fontsource/ibm-plex-sans`, `@fontsource/ibm-plex-mono`,
  `@fontsource/dm-sans`, `@fontsource/dm-mono`. Sin ellas, el sistema degrada a `system-ui`.

## Instalación

```bash
composer require kraftdo/laravel-kraftdo-ui
```

El repo es privado (SSH). En el `composer.json` del proyecto:

```json
"repositories": {
    "kraftdo-ui": { "type": "vcs", "url": "https://github.com/buguenocesar92/laravel-kraftdo-ui.git" }
}
```

En `resources/css/app.css`:

```css
@import "tailwindcss";
@import "../../vendor/kraftdo/laravel-kraftdo-ui/resources/css/kraftdo-ui.css";
@source "../../vendor/kraftdo/laravel-kraftdo-ui/resources/views/**/*.blade.php";
```

Para personalizar los tokens por proyecto, publica el CSS y edítalo:

```bash
php artisan vendor:publish --tag=kraftdo-ui-css   # → resources/css/vendor/kraftdo-ui.css
```

## Uso

```blade
<x-kd::app-shell theme="dark" system="Patentes Comerciales" subtitle="Municipalidad de Graneros" status="online">
    <div style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:18px;">
        <x-kd::kpi :value="number_format($total, 0, ',', '.')" label="Resultado del filtro" />
        <x-kd::kpi :value="$morosos" label="Morosas" tone="danger" />
        <x-kd::kpi :value="$total - $morosos" label="Al día" tone="ok" />
    </div>

    <x-kd::filter-bar :action="route('patentes')">
        <x-kd::field label="Buscar"><input name="buscar" value="{{ $filtros['buscar'] ?? '' }}"></x-kd::field>
        <x-kd::field label="Morosidad">
            <select name="morosa"><option value="">Todas</option><option value="SI">Solo morosas</option></select>
        </x-kd::field>
    </x-kd::filter-bar>

    <x-kd::data-table :columns="['Razón social', 'RUT', 'Tipo', 'Estado']">
        @foreach ($filas as $fila)
            <tr data-muni-row @class(['muni-row--danger' => $fila['morosa'] === 'SI'])>
                <td>{{ $fila['razon_social'] }}</td>
                <td class="muni-num">{{ $fila['rut'] }}</td>
                <td>{{ $fila['tipo'] }}</td>
                <td><x-kd::badge :tone="$fila['morosa'] === 'SI' ? 'danger' : 'ok'">{{ $fila['morosa'] === 'SI' ? 'Morosa' : 'Al día' }}</x-kd::badge></td>
            </tr>
        @endforeach
    </x-kd::data-table>
</x-kd::app-shell>
```

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
| `<x-kd::data-table>` | `columns` (array), `empty`; el slot son los `<tr data-muni-row>` |
| `<x-kd::pagination>` | `current`, `total`, `url` (closure fn(\$p)), `info` |

### Interactivos (requieren Alpine 3)

| Componente | Props principales |
|-----------|-------------------|
| `<x-kd::dropdown>` | `align` (start/end), `width`; slot `trigger` + ítems `<x-kd::dropdown-item>` |
| `<x-kd::dropdown-item>` | `href`, `icon`, `tone` (default/danger) |
| `<x-kd::modal>` | `title`, `maxWidth`; slots `trigger`, `footer` |
| `<x-kd::tabs>` | `tabs` (array de labels), `default`; paneles `<x-kd::tab-panel :index>` |
| `<x-kd::toast-host>` | `position`; colocar UNA vez. Disparar: `$dispatch('muni-toast', {tone, title, message})` |

```blade
{{-- Modal --}}
<x-kd::modal title="Dar de baja la patente">
    <x-slot:trigger><x-kd::button variant="danger">Dar de baja</x-kd::button></x-slot:trigger>
    Se marcará <b>{{ $patente->razon_social }}</b> como cesada.
    <x-slot:footer>
        <x-kd::button variant="ghost" x-on:click="open=false">Cancelar</x-kd::button>
        <x-kd::button x-on:click="open=false; $dispatch('muni-toast',{tone:'ok',message:'Patente dada de baja'})">Confirmar</x-kd::button>
    </x-slot:footer>
</x-kd::modal>

{{-- Toast: colocar el host una vez, disparar desde cualquier parte --}}
<x-kd::toast-host />
<button x-on:click="$dispatch('muni-toast',{tone:'ok',title:'Guardado',message:'Cambios guardados.'})">Guardar</button>
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
Los interactivos usan **Alpine 3 core** (sin plugins): en feria/discapacidad/licencias ya viene
con Filament; en apps sin Filament, `npm i alpinejs` y `Alpine.start()`. El CSS del paquete trae
la regla `[x-cloak]` para evitar el flash inicial.

**Firma del sistema:** la morosidad no es un badge redondo suelto — una fila
`<tr data-muni-row class="muni-row--danger">` pinta una franja de estado en el borde
izquierdo (banda de libro mayor), y los RUT/cifras usan `.muni-num` (mono tabular).

## Demos (`demo/`)

Todas self-contained (Alpine inline, sin CDN).

**Componentes y sistema**
- `index.html` — panel de datos en ambos temas · `interactive.html` — modal/dropdown/tabs/toasts
- `showcase.html` — sala de control cívica con consola viva · `templates.html` — galería de pantallas (landing, login, paneles por rol, error)
- `app.html` — **dashboard de patentes funcional completo** (command palette ⌘K, charts, tabla sortable, drawer, modal, toasts)

**Landings novedosas por sistema** — cada una con identidad propia anclada a su mundo
- `landing-hub.html` — hub del ecosistema (dark mode universal en vivo)
- `landing-licencias.html` — "la ruta" (carretera en perspectiva, señalética vial)
- `landing-discapacidad.html` — "accesibilidad como belleza" (controles reales de a11y)
- `landing-control-acceso.html` — "terminal de vigilancia" (feed biométrico en vivo)
- `landing-patentes.html` — "el libro de rentas" (sello municipal, cifras que respiran)

## Roadmap

- Capa 2: primitivas BlatUI (button/input/dialog…) re-teñidas con estos tokens (requiere Alpine).
- Tema Filament vía `renderHook(PanelsRenderHook::HEAD_END)` que lee los mismos `--kd-*`.
- Pipeline v0 → Blade para componentes complejos nuevos.

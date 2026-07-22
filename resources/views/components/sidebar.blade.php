@props([
    'width' => '240px',
])

{{-- Barra lateral de navegación para dashboards. En móvil se colapsa (toggle con el
     evento `kd-sidebar`). El slot son <x-kd::nav-item> y <x-kd::nav-section>. --}}
<aside
    x-data="{ open: window.innerWidth >= 900 }"
    @kd-sidebar.window="open = !open"
    :class="open ? 'kd-sb--open' : ''"
    class="kd-sb"
    style="--sb-w:{{ $width }};"
    {{ $attributes }}
>
    <div class="kd-sb__inner">
        {{ $slot }}
    </div>
</aside>

@once
    <style>
        .kd-sb { flex-shrink:0; width:var(--sb-w); background:var(--kd-surface); border-right:1px solid var(--kd-border); }
        .kd-sb__inner { position:sticky; top:0; display:flex; flex-direction:column; gap:2px; height:100vh; overflow-y:auto; padding:16px 12px; }
        @media (max-width:899px) {
            .kd-sb { position:fixed; inset:0 auto 0 0; z-index:150; transform:translateX(-100%); transition:transform var(--kd-dur) var(--kd-ease); box-shadow:var(--kd-shadow-lg); }
            .kd-sb--open { transform:translateX(0); }
        }
    </style>
@endonce

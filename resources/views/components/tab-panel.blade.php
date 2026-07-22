@props([
    'index',
])

{{-- Panel de una pestaña. `index` debe coincidir con la posición de su etiqueta en
     el array `tabs` del <x-kd::tabs> padre. --}}
<div
    x-show="active === {{ (int) $index }}"
    x-cloak
    role="tabpanel"
    x-transition:enter="kd-fade" x-transition:enter-start="kd-fade-0" x-transition:enter-end="kd-fade-1"
    {{ $attributes }}
>
    {{ $slot }}
</div>

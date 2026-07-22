@props([
    'align' => 'end',
    'width' => '220px',
])

@php
    $origin = $align === 'start' ? 'left:0;' : 'right:0;';
@endphp

{{-- Menú desplegable (Alpine 3). El slot `trigger` es el botón; el slot por defecto son
     los ítems (usar <x-kd::dropdown-item>). Cierra al hacer click fuera o con Escape. --}}
<div x-data="{ open: false }" @keydown.escape.window="open = false" style="position:relative;display:inline-block;">
    <div @click="open = ! open" :aria-expanded="open" aria-haspopup="menu" style="display:inline-flex;">
        {{ $trigger }}
    </div>

    <div
        x-show="open"
        x-cloak
        @click.outside="open = false"
        x-transition:enter="kd-dd-enter"
        x-transition:enter-start="kd-dd-enter-start"
        x-transition:enter-end="kd-dd-enter-end"
        role="menu"
        {{ $attributes->merge([
            'style' => "position:absolute;top:calc(100% + 6px);{$origin}z-index:50;min-width:{$width};"
                ."padding:5px;background:var(--kd-surface);border:1px solid var(--kd-border);"
                ."border-radius:var(--kd-radius);box-shadow:var(--kd-shadow-lg);"
                ."transform-origin:top ".($align === 'start' ? 'left' : 'right').";",
        ]) }}
    >
        {{ $slot }}
    </div>
</div>

@once
    <style>
        .kd-dd-enter { transition: opacity var(--kd-dur) var(--kd-ease), transform var(--kd-dur) var(--kd-ease); }
        .kd-dd-enter-start { opacity: 0; transform: scale(0.96) translateY(-4px); }
        .kd-dd-enter-end { opacity: 1; transform: scale(1) translateY(0); }
    </style>
@endonce

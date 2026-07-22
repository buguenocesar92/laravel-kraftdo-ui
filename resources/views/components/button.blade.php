@props([
    'variant' => 'primary',
    'size' => 'md',
    'href' => null,
    'icon' => null,
    'type' => 'button',
])

@php
    $tag = $href ? 'a' : 'button';
    $sizes = [
        'sm' => 'padding:6px 12px;font-size:12.5px;',
        'md' => 'padding:9px 16px;font-size:13.5px;',
        'lg' => 'padding:11px 20px;font-size:15px;',
    ];
    $base = 'display:inline-flex;align-items:center;justify-content:center;gap:7px;'
        .'font-family:var(--kd-font-sans);font-weight:600;line-height:1;cursor:pointer;'
        .'border-radius:var(--kd-radius-sm);border:1px solid transparent;text-decoration:none;'
        .'transition:background var(--kd-dur) var(--kd-ease),border-color var(--kd-dur) var(--kd-ease),transform var(--kd-dur) var(--kd-ease),box-shadow var(--kd-dur) var(--kd-ease);'
        .($sizes[$size] ?? $sizes['md']);
@endphp

<{{ $tag }}
    @if ($href) href="{{ $href }}" @else type="{{ $type }}" @endif
    {{ $attributes->merge(['class' => 'kd-btn kd-btn--'.$variant, 'style' => $base]) }}
>
    @if ($icon)<span class="kd-btn__icon" aria-hidden="true">{!! $icon !!}</span>@endif
    {{ $slot }}
</{{ $tag }}>

@once
    <style>
        .kd-btn:focus-visible { outline: none; box-shadow: var(--kd-ring); }
        .kd-btn:active { transform: translateY(1px); }
        .kd-btn__icon { display: inline-flex; }
        .kd-btn__icon svg { width: 15px; height: 15px; }

        .kd-btn--primary { background: var(--kd-accent); color: var(--kd-on-accent); }
        .kd-btn--primary:hover { background: var(--kd-accent-strong); box-shadow: var(--kd-shadow); }

        .kd-btn--ghost { background: transparent; color: var(--kd-text); border-color: var(--kd-border); }
        .kd-btn--ghost:hover { background: var(--kd-surface-2); border-color: var(--kd-border-2); }

        .kd-btn--subtle { background: var(--kd-surface-2); color: var(--kd-text); }
        .kd-btn--subtle:hover { background: var(--kd-surface-3); }

        .kd-btn--danger { background: var(--kd-danger-bg); color: var(--kd-danger-fg); border-color: var(--kd-danger-border); }
        .kd-btn--danger:hover { background: var(--kd-danger-fg); color: #fff; }
    </style>
@endonce

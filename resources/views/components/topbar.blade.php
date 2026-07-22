@props([
    'system',
    'subtitle' => null,
    'status' => 'online',
    'statusLabel' => null,
    'logo' => null,
])

@php
    $statusTone = $status === 'online' ? 'ok' : ($status === 'degraded' ? 'warn' : 'danger');
    $statusText = $statusLabel ?? ['online' => 'En línea', 'degraded' => 'Degradado', 'offline' => 'Caído'][$status] ?? $status;
@endphp

<header
    {{ $attributes->merge([
        'style' => 'position:sticky;top:0;z-index:100;height:var(--kd-topbar-h);'
            .'display:flex;align-items:center;gap:12px;padding:0 16px;'
            .'background:var(--kd-surface);border-bottom:1px solid var(--kd-border);',
    ]) }}
>
    {{-- Logo sobre lienzo blanco: patrón del ecosistema (el logo del sistema necesita
         fondo claro para leerse igual en tema oscuro y claro). --}}
    <div style="height:38px;display:flex;align-items:center;justify-content:center;padding:4px 10px;background:#fff;border-radius:var(--kd-radius-sm);flex-shrink:0;">
        @if ($logo)
            {{ $logo }}
        @else
            <span style="font-family:var(--kd-font-mono);font-weight:700;font-size:13px;color:#0b0f14;letter-spacing:-.02em;">KD</span>
        @endif
    </div>

    <div style="display:flex;flex-direction:column;line-height:1.2;min-width:0;">
        <span style="font-family:var(--kd-font-sans);font-size:13px;font-weight:700;color:var(--kd-text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $system }}</span>
        @if ($subtitle)
            <span style="font-size:11px;color:var(--kd-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $subtitle }}</span>
        @endif
    </div>

    <span style="flex:1;"></span>

    <x-kd::badge :tone="$statusTone">{{ $statusText }}</x-kd::badge>

    {{ $slot }}
</header>

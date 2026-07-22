@props([
    'value',
    'label',
    'tone' => 'neutral',
    'hint' => null,
])

@php
    $accent = [
        'neutral' => 'var(--kd-text)',
        'ok' => 'var(--kd-ok-fg)',
        'warn' => 'var(--kd-warn-fg)',
        'danger' => 'var(--kd-danger-fg)',
        'info' => 'var(--kd-info-fg)',
    ][$tone] ?? 'var(--kd-text)';
@endphp

<div
    {{ $attributes->merge([
        'style' => 'position:relative;padding:16px 18px;background:var(--kd-surface);'
            .'border:1px solid var(--kd-border);border-radius:var(--kd-radius);'
            .'box-shadow:var(--kd-shadow);min-width:130px;',
    ]) }}
>
    {{-- Franja de acento a la izquierda: encoda el tono sin ser un badge redondo. --}}
    <span aria-hidden="true" style="position:absolute;left:0;top:10px;bottom:10px;width:3px;border-radius:0 2px 2px 0;background:{{ $accent }};"></span>

    <div style="font-family:var(--kd-font-mono);font-variant-numeric:tabular-nums;font-size:28px;font-weight:700;line-height:1.05;color:{{ $accent }};padding-left:8px;">
        {{ $value }}
    </div>
    <div style="margin-top:4px;padding-left:8px;font-family:var(--kd-font-sans);font-size:12px;font-weight:500;color:var(--kd-muted);text-transform:uppercase;letter-spacing:.04em;">
        {{ $label }}
    </div>
    @if ($hint)
        <div style="margin-top:2px;padding-left:8px;font-size:11px;color:var(--kd-hint);">{{ $hint }}</div>
    @endif
</div>

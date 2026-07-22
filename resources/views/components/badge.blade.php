@props([
    'tone' => 'neutral',
    'dot' => true,
])

@php
    $tokens = [
        'neutral' => ['fg' => 'var(--kd-muted)', 'bg' => 'var(--kd-surface-3)', 'border' => 'var(--kd-border)'],
        'ok' => ['fg' => 'var(--kd-ok-fg)', 'bg' => 'var(--kd-ok-bg)', 'border' => 'var(--kd-ok-border)'],
        'warn' => ['fg' => 'var(--kd-warn-fg)', 'bg' => 'var(--kd-warn-bg)', 'border' => 'var(--kd-warn-border)'],
        'danger' => ['fg' => 'var(--kd-danger-fg)', 'bg' => 'var(--kd-danger-bg)', 'border' => 'var(--kd-danger-border)'],
        'info' => ['fg' => 'var(--kd-info-fg)', 'bg' => 'var(--kd-info-bg)', 'border' => 'var(--kd-info-border)'],
    ];
    $t = $tokens[$tone] ?? $tokens['neutral'];
@endphp

<span
    {{ $attributes->merge([
        'style' => "display:inline-flex;align-items:center;gap:6px;padding:3px 9px;"
            ."font-family:var(--kd-font-mono);font-size:11px;font-weight:600;line-height:1;"
            ."letter-spacing:.02em;border-radius:999px;"
            ."color:{$t['fg']};background:{$t['bg']};border:1px solid {$t['border']};",
    ]) }}
>
    @if ($dot)
        <span aria-hidden="true" style="width:6px;height:6px;border-radius:50%;background:{{ $t['fg'] }};box-shadow:var(--kd-glow);"></span>
    @endif
    {{ $slot }}
</span>

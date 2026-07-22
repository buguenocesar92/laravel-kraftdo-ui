@props([
    'tone' => 'info',
    'title' => null,
    'icon' => null,
])

@php
    $t = [
        'ok' => ['fg' => 'var(--kd-ok-fg)', 'bg' => 'var(--kd-ok-bg)', 'border' => 'var(--kd-ok-border)'],
        'warn' => ['fg' => 'var(--kd-warn-fg)', 'bg' => 'var(--kd-warn-bg)', 'border' => 'var(--kd-warn-border)'],
        'danger' => ['fg' => 'var(--kd-danger-fg)', 'bg' => 'var(--kd-danger-bg)', 'border' => 'var(--kd-danger-border)'],
        'info' => ['fg' => 'var(--kd-info-fg)', 'bg' => 'var(--kd-info-bg)', 'border' => 'var(--kd-info-border)'],
    ][$tone] ?? null;
    $t ??= ['fg' => 'var(--kd-info-fg)', 'bg' => 'var(--kd-info-bg)', 'border' => 'var(--kd-info-border)'];
@endphp

<div
    role="{{ $tone === 'danger' ? 'alert' : 'status' }}"
    {{ $attributes->merge([
        'style' => "display:flex;gap:11px;padding:12px 14px;border-radius:var(--kd-radius);"
            ."background:{$t['bg']};border:1px solid {$t['border']};"
            ."border-left-width:3px;color:var(--kd-text);font-family:var(--kd-font-sans);font-size:13px;line-height:1.5;",
    ]) }}
>
    <span aria-hidden="true" style="flex-shrink:0;width:16px;height:16px;margin-top:1px;color:{{ $t['fg'] }};">
        {!! $icon ?? '<svg viewBox="0 0 20 20" fill="currentColor" width="16" height="16"><path d="M10 2a8 8 0 100 16 8 8 0 000-16zm0 4a1 1 0 011 1v4a1 1 0 11-2 0V7a1 1 0 011-1zm0 8a1 1 0 100 2 1 1 0 000-2z"/></svg>' !!}
    </span>
    <div style="min-width:0;">
        @if ($title)<div style="font-weight:700;color:{{ $t['fg'] }};margin-bottom:2px;">{{ $title }}</div>@endif
        <div style="color:var(--kd-muted);">{{ $slot }}</div>
    </div>
</div>

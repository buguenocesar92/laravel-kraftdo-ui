@props([
    'title',
    'subtitle' => null,
    'eyebrow' => null,
])

<div {{ $attributes->merge(['style' => 'display:flex;align-items:flex-end;justify-content:space-between;gap:16px;flex-wrap:wrap;margin-bottom:20px;']) }}>
    <div style="min-width:0;">
        @if ($eyebrow)
            <div style="font-family:var(--kd-font-mono);font-size:11px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--kd-accent-text);margin-bottom:6px;">{{ $eyebrow }}</div>
        @endif
        <h1 style="margin:0;font-family:var(--kd-font-sans);font-size:24px;font-weight:800;line-height:1.15;letter-spacing:-.02em;color:var(--kd-text);text-wrap:balance;">{{ $title }}</h1>
        @if ($subtitle)<p style="margin:6px 0 0;font-size:14px;color:var(--kd-muted);max-width:60ch;line-height:1.5;">{{ $subtitle }}</p>@endif
    </div>
    @isset($actions)<div style="display:flex;gap:10px;flex-shrink:0;">{{ $actions }}</div>@endisset
</div>

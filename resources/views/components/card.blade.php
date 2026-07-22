@props([
    'title' => null,
    'subtitle' => null,
    'flush' => false,
])

<section
    class="muni-card"
    {{ $attributes->merge([
        'style' => 'background:var(--kd-surface);border:1px solid var(--kd-border);'
            .'border-radius:var(--kd-radius-lg);box-shadow:var(--kd-shadow);overflow:hidden;'
            .'transition:box-shadow var(--kd-dur) var(--kd-ease);',
    ]) }}
>
    @if ($title || isset($actions))
        <header style="display:flex;align-items:center;justify-content:space-between;gap:12px;padding:14px 18px;border-bottom:1px solid var(--kd-border);">
            <div style="min-width:0;">
                @if ($title)<h3 style="margin:0;font-family:var(--kd-font-sans);font-size:14px;font-weight:700;color:var(--kd-text);">{{ $title }}</h3>@endif
                @if ($subtitle)<p style="margin:2px 0 0;font-size:12px;color:var(--kd-muted);">{{ $subtitle }}</p>@endif
            </div>
            @isset($actions)<div style="display:flex;gap:8px;flex-shrink:0;">{{ $actions }}</div>@endisset
        </header>
    @endif
    <div style="{{ $flush ? '' : 'padding:18px;' }}">{{ $slot }}</div>
</section>

@once
    <style>.muni-card:hover { box-shadow: var(--kd-shadow-md); }</style>
@endonce

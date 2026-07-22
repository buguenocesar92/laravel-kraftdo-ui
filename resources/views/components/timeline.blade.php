@props([
    'items' => [],
])

@php
    // $items: array de ['title'=>, 'time'=>?, 'description'=>?, 'tone'=>? (ok/warn/danger/info/accent)]
@endphp

<ol {{ $attributes->merge(['class' => 'kd-timeline']) }}>
    @foreach ($items as $item)
        @php
            $tone = $item['tone'] ?? 'accent';
            $color = [
                'accent' => 'var(--kd-accent)', 'ok' => 'var(--kd-ok-fg)', 'warn' => 'var(--kd-warn-fg)',
                'danger' => 'var(--kd-danger-fg)', 'info' => 'var(--kd-info-fg)', 'muted' => 'var(--kd-border-2)',
            ][$tone] ?? 'var(--kd-accent)';
        @endphp
        <li class="kd-tl__item">
            <span class="kd-tl__dot" style="--dot:{{ $color }};"></span>
            <div class="kd-tl__content">
                <div class="kd-tl__head">
                    <span class="kd-tl__title">{{ $item['title'] ?? '' }}</span>
                    @if (! empty($item['time']))<time class="kd-tl__time">{{ $item['time'] }}</time>@endif
                </div>
                @if (! empty($item['description']))<p class="kd-tl__desc">{{ $item['description'] }}</p>@endif
            </div>
        </li>
    @endforeach
</ol>

@once
    <style>
        .kd-timeline { list-style:none; margin:0; padding:0; font-family:var(--kd-font-sans); }
        .kd-tl__item { position:relative; display:flex; gap:14px; padding-bottom:18px; }
        .kd-tl__item:not(:last-child)::before { content:""; position:absolute; left:6px; top:16px; bottom:0; width:2px; background:var(--kd-border); }
        .kd-tl__dot { flex-shrink:0; width:14px; height:14px; margin-top:3px; border-radius:50%; background:var(--kd-surface); border:2px solid var(--dot); box-shadow:0 0 0 3px color-mix(in srgb,var(--dot) 15%,transparent),var(--kd-glow); z-index:1; }
        .kd-tl__content { min-width:0; padding-bottom:2px; }
        .kd-tl__head { display:flex; align-items:baseline; gap:10px; flex-wrap:wrap; }
        .kd-tl__title { font-size:13.5px; font-weight:600; color:var(--kd-text); }
        .kd-tl__time { font-family:var(--kd-font-mono); font-size:11px; color:var(--kd-hint); }
        .kd-tl__desc { margin:3px 0 0; font-size:12.5px; color:var(--kd-muted); line-height:1.5; }
    </style>
@endonce

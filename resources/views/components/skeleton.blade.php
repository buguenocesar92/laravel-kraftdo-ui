@props([
    'width' => '100%',
    'height' => '14px',
    'rounded' => 'var(--kd-radius-sm)',
])

<span
    aria-hidden="true"
    {{ $attributes->merge([
        'class' => 'kd-skel',
        'style' => "display:block;width:{$width};height:{$height};border-radius:{$rounded};",
    ]) }}
></span>

@once
    <style>
        .kd-skel { position:relative; overflow:hidden; background:var(--kd-surface-3); }
        .kd-skel::after { content:""; position:absolute; inset:0;
            background:linear-gradient(90deg,transparent,color-mix(in srgb,var(--kd-text) 6%,transparent),transparent);
            transform:translateX(-100%); animation:kd-shimmer 1.4s infinite; }
        @keyframes kd-shimmer { 100% { transform:translateX(100%); } }
        @media (prefers-reduced-motion:reduce) { .kd-skel::after { animation:none; } }
    </style>
@endonce

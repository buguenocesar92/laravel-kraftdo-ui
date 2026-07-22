@props([
    'value' => 0,
    'max' => 5,
    'readonly' => false,
    'name' => null,
    'tone' => 'accent',
])

@php
    $color = ['accent' => 'var(--kd-accent)', 'warn' => 'var(--kd-warn-fg)', 'ok' => 'var(--kd-ok-fg)'][$tone] ?? 'var(--kd-accent)';
@endphp

<div
    x-data="{ value: {{ (float) $value }}, hover: 0, readonly: {{ $readonly ? 'true' : 'false' }} }"
    {{ $attributes->merge(['style' => 'display:inline-flex;align-items:center;gap:3px;']) }}
    role="radiogroup"
>
    @if ($name)<input type="hidden" name="{{ $name }}" :value="value">@endif
    @for ($i = 1; $i <= (int) $max; $i++)
        <button
            type="button"
            @if (! $readonly)
                @click="value = {{ $i }}" @mouseenter="hover = {{ $i }}" @mouseleave="hover = 0"
            @endif
            :aria-checked="value >= {{ $i }}"
            class="muni-star"
            :class="(hover || value) >= {{ $i }} && 'muni-star--on'"
            style="--star:{{ $color }};{{ $readonly ? 'cursor:default;' : '' }}"
            aria-label="{{ $i }} de {{ $max }}"
        >★</button>
    @endfor
</div>

@once
    <style>
        .muni-star { background:none; border:none; padding:0 1px; font-size:20px; line-height:1; color:var(--kd-border-2); cursor:pointer; transition:color var(--kd-dur) var(--kd-ease),transform var(--kd-dur) var(--kd-ease); }
        .muni-star:hover { transform:scale(1.15); }
        .muni-star:focus-visible { outline:none; box-shadow:var(--kd-ring); border-radius:4px; }
        .muni-star--on { color:var(--star); text-shadow:var(--kd-glow); }
    </style>
@endonce

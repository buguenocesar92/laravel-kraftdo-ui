@props([
    'label' => null,
    'name' => null,
    'type' => 'text',
    'error' => null,
    'hint' => null,
    'icon' => null,
    'required' => false,
])

@php $id = $name ? 'muni-'.$name : 'muni-'.uniqid(); @endphp

<div style="display:flex;flex-direction:column;gap:6px;">
    @if ($label)
        <label for="{{ $id }}" style="font-family:var(--kd-font-sans);font-size:12.5px;font-weight:600;color:var(--kd-text);">
            {{ $label }}@if ($required)<span style="color:var(--kd-danger-fg);margin-left:2px;">*</span>@endif
        </label>
    @endif

    <div style="position:relative;display:flex;align-items:center;">
        @if ($icon)
            <span aria-hidden="true" style="position:absolute;left:11px;display:inline-flex;color:var(--kd-muted);pointer-events:none;">{!! $icon !!}</span>
        @endif
        <input
            id="{{ $id }}"
            type="{{ $type }}"
            @if ($name) name="{{ $name }}" @endif
            @if ($required) required @endif
            @if ($error) aria-invalid="true" @endif
            {{ $attributes->merge([
                'class' => 'muni-input',
                'style' => 'width:100%;padding:10px 12px;'.($icon ? 'padding-left:36px;' : '')
                    .'font-family:var(--kd-font-sans);font-size:13.5px;color:var(--kd-text);'
                    .'background:var(--kd-surface);border:1px solid '.($error ? 'var(--kd-danger-border)' : 'var(--kd-border)').';'
                    .'border-radius:var(--kd-radius-sm);transition:border-color var(--kd-dur) var(--kd-ease),box-shadow var(--kd-dur) var(--kd-ease);',
            ]) }}
        >
    </div>

    @if ($error)
        <span style="font-size:11.5px;color:var(--kd-danger-fg);">{{ $error }}</span>
    @elseif ($hint)
        <span style="font-size:11.5px;color:var(--kd-hint);">{{ $hint }}</span>
    @endif
</div>

@once
    <style>
        .muni-input::placeholder { color: var(--kd-hint); }
        .muni-input:focus { outline: none; border-color: var(--kd-accent); box-shadow: var(--kd-ring); }
        .muni-input:disabled { background: var(--kd-surface-2); color: var(--kd-muted); cursor: not-allowed; }
    </style>
@endonce

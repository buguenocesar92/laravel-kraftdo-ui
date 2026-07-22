@props([
    'label' => null,
    'name' => null,
    'options' => [],
    'selected' => null,
    'placeholder' => null,
    'error' => null,
    'hint' => null,
    'required' => false,
])

@php $id = $name ? 'muni-'.$name : 'muni-'.uniqid(); @endphp

<div style="display:flex;flex-direction:column;gap:6px;">
    @if ($label)
        <label for="{{ $id }}" style="font-family:var(--kd-font-sans);font-size:12.5px;font-weight:600;color:var(--kd-text);">
            {{ $label }}@if ($required)<span style="color:var(--kd-danger-fg);margin-left:2px;">*</span>@endif
        </label>
    @endif

    <div style="position:relative;">
        <select
            id="{{ $id }}"
            @if ($name) name="{{ $name }}" @endif
            @if ($required) required @endif
            {{ $attributes->merge([
                'class' => 'muni-select',
                'style' => 'width:100%;padding:10px 34px 10px 12px;appearance:none;'
                    .'font-family:var(--kd-font-sans);font-size:13.5px;color:var(--kd-text);'
                    .'background:var(--kd-surface);border:1px solid '.($error ? 'var(--kd-danger-border)' : 'var(--kd-border)').';'
                    .'border-radius:var(--kd-radius-sm);cursor:pointer;transition:border-color var(--kd-dur) var(--kd-ease),box-shadow var(--kd-dur) var(--kd-ease);',
            ]) }}
        >
            @if ($placeholder)<option value="">{{ $placeholder }}</option>@endif
            @if (! empty($options))
                @foreach ($options as $val => $text)
                    <option value="{{ $val }}" @selected((string) $selected === (string) $val)>{{ $text }}</option>
                @endforeach
            @else
                {{ $slot }}
            @endif
        </select>
        <span aria-hidden="true" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);pointer-events:none;color:var(--kd-muted);">
            <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" width="14" height="14"><path d="M4 6l4 4 4-4" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </span>
    </div>

    @if ($error)<span style="font-size:11.5px;color:var(--kd-danger-fg);">{{ $error }}</span>
    @elseif ($hint)<span style="font-size:11.5px;color:var(--kd-hint);">{{ $hint }}</span>@endif
</div>

@once
    <style>
        .muni-select:focus { outline: none; border-color: var(--kd-accent); box-shadow: var(--kd-ring); }
    </style>
@endonce

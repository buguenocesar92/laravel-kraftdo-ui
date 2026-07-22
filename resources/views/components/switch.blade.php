@props([
    'label' => null,
    'name' => null,
    'checked' => false,
    'description' => null,
])

@php $id = $name ? 'kd-'.$name : 'kd-'.uniqid(); @endphp

<label for="{{ $id }}" style="display:flex;align-items:flex-start;gap:11px;cursor:pointer;">
    <span x-data="{ on: {{ $checked ? 'true' : 'false' }} }" style="position:relative;flex-shrink:0;margin-top:1px;">
        <input type="checkbox" id="{{ $id }}" @if ($name) name="{{ $name }}" @endif @checked($checked)
               x-model="on" {{ $attributes }}
               style="position:absolute;opacity:0;width:0;height:0;">
        <span class="kd-switch" :class="on && 'kd-switch--on'">
            <span class="kd-switch__thumb"></span>
        </span>
    </span>
    @if ($label || $description)
        <span style="display:flex;flex-direction:column;gap:1px;">
            @if ($label)<span style="font-family:var(--kd-font-sans);font-size:13.5px;font-weight:600;color:var(--kd-text);">{{ $label }}</span>@endif
            @if ($description)<span style="font-size:12px;color:var(--kd-muted);line-height:1.4;">{{ $description }}</span>@endif
        </span>
    @endif
</label>

@once
    <style>
        .kd-switch { display:inline-block; width:38px; height:22px; border-radius:999px; background:var(--kd-surface-3); border:1px solid var(--kd-border); transition:background var(--kd-dur) var(--kd-ease),border-color var(--kd-dur) var(--kd-ease); }
        .kd-switch__thumb { display:block; width:16px; height:16px; margin:2px; border-radius:50%; background:var(--kd-surface); box-shadow:0 1px 3px rgba(0,0,0,.25); transition:transform var(--kd-dur) var(--kd-ease); }
        .kd-switch--on { background:var(--kd-accent); border-color:var(--kd-accent); }
        .kd-switch--on .kd-switch__thumb { transform:translateX(16px); }
        input:focus-visible + .kd-switch { box-shadow:var(--kd-ring); }
    </style>
@endonce

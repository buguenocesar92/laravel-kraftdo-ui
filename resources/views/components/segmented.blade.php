@props([
    'name' => null,
    'options' => [],
    'value' => null,
])

{{-- Control segmentado (toggle de filtro): alternativa moderna al <select> para pocas
     opciones. Con `name` genera radios reales (funciona sin JS, submit del form nativo);
     sin `name` es puramente visual/enlaces (usar el slot). --}}
<div role="group" {{ $attributes->merge(['style' => 'display:inline-flex;padding:3px;gap:2px;background:var(--kd-surface-2);border:1px solid var(--kd-border);border-radius:var(--kd-radius-sm);']) }}>
    @if (! empty($options) && $name)
        @foreach ($options as $val => $label)
            @php $id = $name.'-'.$loop->index; $active = (string) $value === (string) $val; @endphp
            <label for="{{ $id }}" class="kd-seg {{ $active ? 'kd-seg--on' : '' }}">
                <input type="radio" id="{{ $id }}" name="{{ $name }}" value="{{ $val }}" @checked($active)
                       style="position:absolute;opacity:0;width:0;height:0;" onchange="this.form && this.form.submit()">
                {{ $label }}
            </label>
        @endforeach
    @else
        {{ $slot }}
    @endif
</div>

@once
    <style>
        .kd-seg { display:inline-flex;align-items:center;justify-content:center;padding:6px 14px;
            font-family:var(--kd-font-sans);font-size:12.5px;font-weight:600;color:var(--kd-muted);
            border-radius:calc(var(--kd-radius-sm) - 2px);cursor:pointer;white-space:nowrap;
            transition:background var(--kd-dur) var(--kd-ease),color var(--kd-dur) var(--kd-ease); }
        .kd-seg:hover { color:var(--kd-text); }
        .kd-seg:has(input:focus-visible) { box-shadow:var(--kd-ring); }
        .kd-seg--on { background:var(--kd-surface);color:var(--kd-text);box-shadow:var(--kd-shadow); }
    </style>
@endonce

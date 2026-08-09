@props([
    'steps' => [],
    'current' => 0,
    'orientation' => 'horizontal',
])

@php
    // $steps: array de strings o de ['label'=>, 'hint'=>?]. $current = índice activo (0-based).
    $current = (int) $current;
    $vertical = $orientation === 'vertical';
@endphp

<ol {{ $attributes->merge(['class' => 'kd-stepper '.($vertical ? 'kd-stepper--v' : '')]) }}>
    @foreach ($steps as $i => $step)
        @php
            $label = is_array($step) ? ($step['label'] ?? '') : $step;
            $hint = is_array($step) ? ($step['hint'] ?? null) : null;
            $state = $i < $current ? 'done' : ($i === $current ? 'active' : 'todo');
        @endphp
        <li class="kd-step kd-step--{{ $state }}" @if ($state === 'active') aria-current="step" @endif>
            <span class="kd-step__marker">
                @if ($state === 'done')
                    <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" width="12" height="12"><path d="M3.5 8.5l3 3 6-6.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                @else
                    <span class="kd-step__num">{{ $i + 1 }}</span>
                @endif
            </span>
            <span class="kd-step__body">
                <span class="kd-step__label">{{ $label }}</span>
                @if ($hint)<span class="kd-step__hint">{{ $hint }}</span>@endif
            </span>
        </li>
    @endforeach
</ol>

@once
    <style>
        .kd-stepper { list-style:none; margin:0; padding:0; display:flex; gap:0; font-family:var(--kd-font-sans); }
        .kd-step { flex:1; display:flex; align-items:center; gap:10px; position:relative; min-width:0; }
        .kd-step:not(:last-child)::after { content:""; position:absolute; left:calc(14px + 26px); right:-10px; top:14px; height:2px; background:var(--kd-border); z-index:0; }
        .kd-step--done:not(:last-child)::after { background:var(--kd-accent); }
        .kd-step__marker { position:relative; z-index:1; flex-shrink:0; width:28px; height:28px; border-radius:50%; display:grid; place-items:center; border:2px solid var(--kd-border); background:var(--kd-surface); color:var(--kd-muted); transition:all var(--kd-dur) var(--kd-ease); }
        .kd-step__num { font-family:var(--kd-font-mono); font-size:12px; font-weight:600; }
        .kd-step__body { display:flex; flex-direction:column; min-width:0; }
        .kd-step__label { font-size:12.5px; font-weight:600; color:var(--kd-muted); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .kd-step__hint { font-size:11px; color:var(--kd-hint); }
        .kd-step--active .kd-step__marker { border-color:var(--kd-accent); color:var(--kd-accent-text); box-shadow:var(--kd-ring); }
        .kd-step--active .kd-step__label { color:var(--kd-text); }
        .kd-step--done .kd-step__marker { border-color:var(--kd-accent); background:var(--kd-accent); color:var(--kd-on-accent); }
        .kd-step--done .kd-step__label { color:var(--kd-text); }

        .kd-stepper--v { flex-direction:column; gap:4px; }
        .kd-stepper--v .kd-step { flex:none; align-items:flex-start; padding-bottom:14px; }
        .kd-stepper--v .kd-step:not(:last-child)::after { left:13px; right:auto; top:28px; bottom:0; width:2px; height:auto; }
        .kd-stepper--v .kd-step__body { padding-top:4px; }
    </style>
@endonce

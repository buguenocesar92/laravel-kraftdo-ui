@props([
    'segments' => [],
    'size' => 150,
    'thickness' => 18,
    'total' => null,
    'centerLabel' => null,
])

@php
    // $segments: array de ['label'=>, 'value'=>, 'tone'=>? o 'color'=>?]
    $toneColor = [
        'accent' => 'var(--kd-accent)', 'ok' => 'var(--kd-ok-fg)', 'warn' => 'var(--kd-warn-fg)',
        'danger' => 'var(--kd-danger-fg)', 'info' => 'var(--kd-info-fg)', 'muted' => 'var(--kd-border-2)',
    ];
    $sum = $total;
    if ($sum === null) { $sum = 0; foreach ($segments as $s) { $sum += (float) $s['value']; } }
    $sum = $sum ?: 1;
    $r = 42;
    $circ = 2 * M_PI * $r;
    $offset = 0;
    $arcs = [];
    foreach ($segments as $s) {
        $frac = (float) $s['value'] / $sum;
        $len = $circ * $frac;
        $color = $s['color'] ?? ($toneColor[$s['tone'] ?? 'accent'] ?? 'var(--kd-accent)');
        $arcs[] = ['len' => $len, 'gap' => $circ - $len, 'off' => -$offset, 'color' => $color, 'label' => $s['label'] ?? '', 'value' => $s['value']];
        $offset += $len;
    }
@endphp

<div {{ $attributes->merge(['style' => 'display:inline-flex;align-items:center;gap:20px;flex-wrap:wrap;']) }}>
    <div style="position:relative;width:{{ $size }}px;height:{{ $size }}px;flex-shrink:0;">
        <svg viewBox="0 0 100 100" style="width:100%;height:100%;transform:rotate(-90deg);">
            <circle cx="50" cy="50" r="{{ $r }}" fill="none" stroke="var(--kd-surface-3)" stroke-width="{{ $thickness }}"/>
            @foreach ($arcs as $a)
                <circle cx="50" cy="50" r="{{ $r }}" fill="none" stroke="{{ $a['color'] }}" stroke-width="{{ $thickness }}"
                        stroke-dasharray="{{ round($a['len'], 2) }} {{ round($a['gap'], 2) }}" stroke-dashoffset="{{ round($a['off'], 2) }}"
                        style="transition:stroke-dasharray .7s var(--kd-ease);"/>
            @endforeach
        </svg>
        @if ($centerLabel !== null)
            <div style="position:absolute;inset:0;display:grid;place-items:center;text-align:center;">
                <span style="font-family:var(--kd-font-mono);font-variant-numeric:tabular-nums;font-size:{{ round($size / 5.5) }}px;font-weight:700;color:var(--kd-text);line-height:1;">{{ $centerLabel }}</span>
            </div>
        @endif
    </div>
    <div style="display:flex;flex-direction:column;gap:8px;">
        @foreach ($arcs as $a)
            <div style="display:flex;align-items:center;gap:9px;font-family:var(--kd-font-sans);font-size:12.5px;">
                <span style="width:10px;height:10px;border-radius:3px;background:{{ $a['color'] }};flex-shrink:0;box-shadow:var(--kd-glow);"></span>
                <span style="color:var(--kd-muted);flex:1;">{{ $a['label'] }}</span>
                <span style="font-family:var(--kd-font-mono);font-variant-numeric:tabular-nums;font-weight:600;color:var(--kd-text);">{{ $a['value'] }}</span>
            </div>
        @endforeach
    </div>
</div>

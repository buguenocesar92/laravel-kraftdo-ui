@props([
    'items' => [],
    'multiple' => false,
    'default' => null,
])

@php
    // $items: array de ['title'=>, 'content'=>] o usar el slot con <x-kd::accordion-item>.
@endphp

<div
    x-data="{ open: {{ $default !== null ? (int) $default : 'null' }}, multiple: {{ $multiple ? 'true' : 'false' }}, opened: [],
        toggle(i){ if(this.multiple){ this.opened = this.opened.includes(i) ? this.opened.filter(x=>x!==i) : [...this.opened, i]; } else { this.open = this.open===i ? null : i; } },
        isOpen(i){ return this.multiple ? this.opened.includes(i) : this.open===i; } }"
    {{ $attributes->merge(['class' => 'kd-acc']) }}
>
    @if (! empty($items))
        @foreach ($items as $i => $item)
            <div class="kd-acc__item">
                <button type="button" class="kd-acc__head" @click="toggle({{ $i }})" :aria-expanded="isOpen({{ $i }})">
                    <span>{{ $item['title'] ?? '' }}</span>
                    <span class="kd-acc__chevron" :class="isOpen({{ $i }}) && 'kd-acc__chevron--open'">
                        <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.8" width="15" height="15"><path d="M4 6l4 4 4-4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                </button>
                <div class="kd-acc__panel" x-show="isOpen({{ $i }})" x-cloak
                     x-transition:enter="kd-acc-enter" x-transition:enter-start="kd-acc-0" x-transition:enter-end="kd-acc-1">
                    <div class="kd-acc__body">{{ $item['content'] ?? '' }}</div>
                </div>
            </div>
        @endforeach
    @else
        {{ $slot }}
    @endif
</div>

@once
    <style>
        .kd-acc { border:1px solid var(--kd-border); border-radius:var(--kd-radius); overflow:hidden; background:var(--kd-surface); }
        .kd-acc__item + .kd-acc__item { border-top:1px solid var(--kd-border); }
        .kd-acc__head { display:flex; align-items:center; justify-content:space-between; gap:12px; width:100%; padding:15px 18px; background:transparent; border:none; cursor:pointer; font-family:var(--kd-font-sans); font-size:14px; font-weight:600; color:var(--kd-text); text-align:left; transition:background var(--kd-dur) var(--kd-ease); }
        .kd-acc__head:hover { background:var(--kd-surface-2); }
        .kd-acc__head:focus-visible { outline:none; box-shadow:inset var(--kd-ring); }
        .kd-acc__chevron { color:var(--kd-muted); transition:transform var(--kd-dur) var(--kd-ease); display:inline-flex; }
        .kd-acc__chevron--open { transform:rotate(180deg); }
        .kd-acc__body { padding:0 18px 16px; font-family:var(--kd-font-sans); font-size:13.5px; color:var(--kd-muted); line-height:1.6; }
        .kd-acc-enter { transition:opacity var(--kd-dur) var(--kd-ease), transform var(--kd-dur) var(--kd-ease); }
        .kd-acc-0 { opacity:0; transform:translateY(-6px); }
        .kd-acc-1 { opacity:1; transform:translateY(0); }
    </style>
@endonce

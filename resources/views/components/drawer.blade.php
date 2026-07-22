@props([
    'title' => null,
    'side' => 'right',
    'width' => '400px',
])

@php $isRight = $side !== 'left'; @endphp

{{-- Panel lateral deslizante (Alpine 3). El slot `trigger` abre; Escape / click en el fondo
     cierran. Bloquea el scroll del body. --}}
<div x-data="{ open:false }" @keydown.escape.window="open=false" x-effect="document.body.style.overflow = open ? 'hidden' : ''" {{ $attributes }}>
    @isset($trigger)<div @click="open=true" style="display:inline-flex;">{{ $trigger }}</div>@endisset

    <template x-teleport="body">
        <div x-show="open" x-cloak style="position:fixed;inset:0;z-index:210;">
            <div x-show="open" @click="open=false"
                 x-transition:enter="kd-fade" x-transition:enter-start="kd-fade-0" x-transition:enter-end="kd-fade-1"
                 x-transition:leave="kd-fade" x-transition:leave-start="kd-fade-1" x-transition:leave-end="kd-fade-0"
                 style="position:absolute;inset:0;background:rgba(10,14,20,.5);backdrop-filter:blur(2px);"></div>

            <div x-show="open" role="dialog" aria-modal="true"
                 x-transition:enter="kd-drawer" x-transition:enter-start="{{ $isRight ? 'kd-drawer-r0' : 'kd-drawer-l0' }}" x-transition:enter-end="kd-drawer-1"
                 x-transition:leave="kd-drawer" x-transition:leave-start="kd-drawer-1" x-transition:leave-end="{{ $isRight ? 'kd-drawer-r0' : 'kd-drawer-l0' }}"
                 style="position:absolute;top:0;bottom:0;{{ $isRight ? 'right:0;' : 'left:0;' }}width:{{ $width }};max-width:92vw;display:flex;flex-direction:column;background:var(--kd-surface);border-{{ $isRight ? 'left' : 'right' }}:1px solid var(--kd-border);box-shadow:var(--kd-shadow-lg);">
                <header style="display:flex;align-items:center;justify-content:space-between;gap:12px;padding:16px 20px;border-bottom:1px solid var(--kd-border);">
                    <h2 style="margin:0;font-family:var(--kd-font-sans);font-size:15px;font-weight:700;color:var(--kd-text);">{{ $title }}</h2>
                    <button type="button" @click="open=false" aria-label="Cerrar" class="kd-drawer__x">
                        <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" width="16" height="16"><path d="M5 5l10 10M15 5L5 15" stroke-linecap="round"/></svg>
                    </button>
                </header>
                <div style="flex:1;overflow-y:auto;padding:20px;font-family:var(--kd-font-sans);font-size:14px;color:var(--kd-text);line-height:1.6;">{{ $slot }}</div>
                @isset($footer)<footer style="padding:14px 20px;border-top:1px solid var(--kd-border);background:var(--kd-surface-2);display:flex;justify-content:flex-end;gap:10px;">{{ $footer }}</footer>@endisset
            </div>
        </div>
    </template>
</div>

@once
    <style>
        .kd-drawer__x { display:inline-flex; padding:6px; border:none; background:transparent; color:var(--kd-muted); border-radius:var(--kd-radius-sm); cursor:pointer; transition:background var(--kd-dur) var(--kd-ease); }
        .kd-drawer__x:hover { background:var(--kd-surface-3); color:var(--kd-text); }
        .kd-fade { transition:opacity var(--kd-dur) var(--kd-ease); } .kd-fade-0 { opacity:0; } .kd-fade-1 { opacity:1; }
        .kd-drawer { transition:transform .28s var(--kd-ease); }
        .kd-drawer-r0 { transform:translateX(100%); } .kd-drawer-l0 { transform:translateX(-100%); } .kd-drawer-1 { transform:translateX(0); }
    </style>
@endonce

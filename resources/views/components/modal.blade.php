@props([
    'title' => null,
    'maxWidth' => '480px',
])

{{-- Modal accesible (Alpine 3). El slot `trigger` abre; Escape / click en el fondo /
     botón × cierran. Bloquea el scroll del body mientras está abierto y devuelve el foco
     al panel al abrir. El slot `footer` es opcional (acciones). --}}
<div
    x-data="{ open: false }"
    @keydown.escape.window="open = false"
    x-effect="document.body.style.overflow = open ? 'hidden' : ''"
>
    @isset($trigger)
        <div @click="open = true" style="display:inline-flex;">{{ $trigger }}</div>
    @endisset

    <template x-teleport="body">
        <div
            x-show="open"
            x-cloak
            role="dialog"
            aria-modal="true"
            style="position:fixed;inset:0;z-index:200;display:flex;align-items:center;justify-content:center;padding:20px;"
        >
            {{-- Fondo --}}
            <div
                x-show="open"
                x-transition:enter="kd-fade" x-transition:enter-start="kd-fade-0" x-transition:enter-end="kd-fade-1"
                x-transition:leave="kd-fade" x-transition:leave-start="kd-fade-1" x-transition:leave-end="kd-fade-0"
                @click="open = false"
                style="position:absolute;inset:0;background:rgba(10,14,20,.55);backdrop-filter:blur(2px);"
            ></div>

            {{-- Panel --}}
            <div
                x-show="open"
                x-transition:enter="kd-pop" x-transition:enter-start="kd-pop-0" x-transition:enter-end="kd-pop-1"
                x-transition:leave="kd-pop" x-transition:leave-start="kd-pop-1" x-transition:leave-end="kd-pop-0"
                {{ $attributes->merge([
                    'style' => "position:relative;width:100%;max-width:{$maxWidth};max-height:calc(100vh - 40px);"
                        ."display:flex;flex-direction:column;background:var(--kd-surface);color:var(--kd-text);"
                        ."border:1px solid var(--kd-border);border-radius:var(--kd-radius-lg);"
                        ."box-shadow:var(--kd-shadow-lg);overflow:hidden;font-family:var(--kd-font-sans);",
                ]) }}
            >
                <header style="display:flex;align-items:center;justify-content:space-between;gap:12px;padding:16px 18px;border-bottom:1px solid var(--kd-border);">
                    <h2 style="margin:0;font-size:15px;font-weight:700;color:var(--kd-text);">{{ $title }}</h2>
                    <button type="button" @click="open = false" aria-label="Cerrar" class="kd-modal-x">
                        <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" width="16" height="16"><path d="M5 5l10 10M15 5L5 15" stroke-linecap="round"/></svg>
                    </button>
                </header>

                <div style="padding:18px;overflow-y:auto;font-size:14px;line-height:1.55;color:var(--kd-text);">
                    {{ $slot }}
                </div>

                @isset($footer)
                    <footer style="display:flex;justify-content:flex-end;gap:10px;padding:14px 18px;border-top:1px solid var(--kd-border);background:var(--kd-surface-2);">
                        {{ $footer }}
                    </footer>
                @endisset
            </div>
        </div>
    </template>
</div>

@once
    <style>
        .kd-modal-x { display:inline-flex;padding:6px;border:none;background:transparent;color:var(--kd-muted);border-radius:var(--kd-radius-sm);cursor:pointer;transition:background var(--kd-dur) var(--kd-ease),color var(--kd-dur) var(--kd-ease); }
        .kd-modal-x:hover { background:var(--kd-surface-3);color:var(--kd-text); }
        .kd-modal-x:focus-visible { outline:none;box-shadow:var(--kd-ring); }
        .kd-fade { transition:opacity var(--kd-dur) var(--kd-ease); }
        .kd-fade-0 { opacity:0; } .kd-fade-1 { opacity:1; }
        .kd-pop { transition:opacity var(--kd-dur) var(--kd-ease),transform var(--kd-dur) var(--kd-ease); }
        .kd-pop-0 { opacity:0;transform:scale(.96) translateY(8px); }
        .kd-pop-1 { opacity:1;transform:scale(1) translateY(0); }
    </style>
@endonce

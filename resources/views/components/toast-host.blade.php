@props([
    'position' => 'bottom-right',
])

@php
    $pos = [
        'bottom-right' => 'bottom:16px;right:16px;align-items:flex-end;',
        'bottom-left' => 'bottom:16px;left:16px;align-items:flex-start;',
        'top-right' => 'top:16px;right:16px;align-items:flex-end;',
        'top-left' => 'top:16px;left:16px;align-items:flex-start;',
    ][$position] ?? 'bottom:16px;right:16px;align-items:flex-end;';
@endphp

{{-- Host de notificaciones. Colocar UNA vez (p. ej. dentro de <x-kd::app-shell>).
     Disparar desde cualquier parte:
       $dispatch('muni-toast', { message: 'Guardado', tone: 'ok', title: 'Listo' })
     o en JS: window.dispatchEvent(new CustomEvent('muni-toast', { detail: {...} })) --}}
<div
    x-data="{
        items: [],
        push(detail) {
            const id = Date.now() + Math.random();
            this.items.push({ id, tone: detail.tone || 'info', title: detail.title || null, message: detail.message || '' });
            setTimeout(() => this.remove(id), detail.duration || 4500);
        },
        remove(id) { this.items = this.items.filter(i => i.id !== id); }
    }"
    @muni-toast.window="push($event.detail || {})"
    style="position:fixed;z-index:300;display:flex;flex-direction:column;gap:10px;max-width:360px;{{ $pos }}"
    aria-live="polite"
    aria-atomic="false"
>
    <template x-for="item in items" :key="item.id">
        <div
            x-transition:enter="muni-toast-enter"
            x-transition:enter-start="muni-toast-enter-start"
            x-transition:enter-end="muni-toast-enter-end"
            x-transition:leave="muni-toast-leave"
            x-transition:leave-start="muni-toast-leave-start"
            x-transition:leave-end="muni-toast-leave-end"
            class="muni-toast"
            :class="'muni-toast--' + item.tone"
            role="status"
        >
            <span class="muni-toast__dot" aria-hidden="true"></span>
            <div style="min-width:0;flex:1;">
                <template x-if="item.title"><div class="muni-toast__title" x-text="item.title"></div></template>
                <div class="muni-toast__msg" x-text="item.message"></div>
            </div>
            <button type="button" @click="remove(item.id)" aria-label="Cerrar" class="muni-toast__x">
                <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" width="14" height="14"><path d="M5 5l10 10M15 5L5 15" stroke-linecap="round"/></svg>
            </button>
        </div>
    </template>
</div>

@once
    <style>
        .muni-toast { display:flex;align-items:flex-start;gap:10px;padding:12px 14px;
            background:var(--kd-surface);border:1px solid var(--kd-border);border-left-width:3px;
            border-radius:var(--kd-radius);box-shadow:var(--kd-shadow-lg);font-family:var(--kd-font-sans); }
        .muni-toast__dot { flex-shrink:0;width:8px;height:8px;margin-top:5px;border-radius:50%;box-shadow:var(--kd-glow); }
        .muni-toast__title { font-size:13px;font-weight:700;color:var(--kd-text);margin-bottom:1px; }
        .muni-toast__msg { font-size:12.5px;color:var(--kd-muted);line-height:1.45; }
        .muni-toast__x { flex-shrink:0;display:inline-flex;padding:3px;border:none;background:transparent;color:var(--kd-hint);border-radius:var(--kd-radius-sm);cursor:pointer;transition:color var(--kd-dur) var(--kd-ease); }
        .muni-toast__x:hover { color:var(--kd-text); }
        .muni-toast--ok { border-left-color:var(--kd-ok-fg); } .muni-toast--ok .muni-toast__dot { background:var(--kd-ok-fg);color:var(--kd-ok-fg); }
        .muni-toast--warn { border-left-color:var(--kd-warn-fg); } .muni-toast--warn .muni-toast__dot { background:var(--kd-warn-fg);color:var(--kd-warn-fg); }
        .muni-toast--danger { border-left-color:var(--kd-danger-fg); } .muni-toast--danger .muni-toast__dot { background:var(--kd-danger-fg);color:var(--kd-danger-fg); }
        .muni-toast--info { border-left-color:var(--kd-info-fg); } .muni-toast--info .muni-toast__dot { background:var(--kd-info-fg);color:var(--kd-info-fg); }
        .muni-toast-enter { transition:opacity var(--kd-dur) var(--kd-ease),transform var(--kd-dur) var(--kd-ease); }
        .muni-toast-enter-start { opacity:0;transform:translateY(8px) scale(.98); }
        .muni-toast-enter-end { opacity:1;transform:translateY(0) scale(1); }
        .muni-toast-leave { transition:opacity var(--kd-dur) var(--kd-ease),transform var(--kd-dur) var(--kd-ease); }
        .muni-toast-leave-start { opacity:1;transform:translateX(0); }
        .muni-toast-leave-end { opacity:0;transform:translateX(12px); }
    </style>
@endonce

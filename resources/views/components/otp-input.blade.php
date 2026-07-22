@props([
    'length' => 6,
    'name' => 'code',
])

{{-- Entrada de código de un solo uso / MFA (Alpine 3). Autofoco entre casillas, pega
     un código completo, retrocede con Backspace. El valor va en un input oculto. --}}
<div
    x-data="{
        digits: Array({{ (int) $length }}).fill(''),
        get code(){ return this.digits.join(''); },
        onInput(i, e){
            const v = e.target.value.replace(/\D/g,'');
            if(v.length > 1){ this.paste(v); return; }
            this.digits[i] = v;
            if(v && i < {{ (int) $length - 1 }}) this.$refs['d'+(i+1)].focus();
        },
        onKey(i, e){
            if(e.key==='Backspace' && !this.digits[i] && i>0){ this.$refs['d'+(i-1)].focus(); }
        },
        paste(v){ v.split('').slice(0,{{ (int) $length }}).forEach((c,i)=>{ this.digits[i]=c; }); this.$nextTick(()=>{ const last=Math.min(v.length,{{ (int) $length }})-1; this.$refs['d'+Math.max(last,0)].focus(); }); }
    }"
    {{ $attributes->merge(['style' => 'display:flex;gap:9px;']) }}
>
    <input type="hidden" name="{{ $name }}" :value="code">
    @for ($i = 0; $i < (int) $length; $i++)
        <input
            x-ref="d{{ $i }}"
            x-model="digits[{{ $i }}]"
            @input="onInput({{ $i }}, $event)"
            @keydown="onKey({{ $i }}, $event)"
            @paste.prevent="paste(($event.clipboardData||window.clipboardData).getData('text').replace(/\D/g,''))"
            inputmode="numeric" maxlength="1" autocomplete="one-time-code"
            class="kd-otp"
            aria-label="Dígito {{ $i + 1 }}"
        >
    @endfor
</div>

@once
    <style>
        .kd-otp { width:46px; height:54px; text-align:center; font-family:var(--kd-font-mono); font-size:22px; font-weight:700; color:var(--kd-text);
            background:var(--kd-surface); border:1px solid var(--kd-border); border-radius:var(--kd-radius-sm);
            transition:border-color var(--kd-dur) var(--kd-ease),box-shadow var(--kd-dur) var(--kd-ease); }
        .kd-otp:focus { outline:none; border-color:var(--kd-accent); box-shadow:var(--kd-ring); }
        @media (max-width:420px){ .kd-otp { width:40px; height:48px; font-size:19px; } }
    </style>
@endonce

@props([
    'theme' => 'light',
    'title' => 'Ingresar',
    'system' => 'Panel',
    'subtitle' => null,
    'logo' => null,
])

<!DOCTYPE html>
<html lang="es" data-kd-theme="{{ $theme }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} · {{ $system }}</title>
    {{ $head ?? '' }}
    <style>
        *,*::before,*::after{ box-sizing:border-box; }
        body{ margin:0; min-height:100vh; display:grid; grid-template-columns:1fr; background:var(--kd-bg); color:var(--kd-text); font-family:var(--kd-font-sans); }
        @media (min-width:900px){ body{ grid-template-columns:1.05fr .95fr; } }
        .kd-auth-aside{ display:none; position:relative; overflow:hidden; padding:48px; flex-direction:column; justify-content:space-between; background:var(--kd-surface); border-right:1px solid var(--kd-border); }
        @media (min-width:900px){ .kd-auth-aside{ display:flex; } }
        .kd-auth-aside::before{ content:""; position:absolute; inset:0; opacity:.5;
            background-image:linear-gradient(color-mix(in srgb,var(--kd-accent) 6%,transparent) 1px,transparent 1px),linear-gradient(90deg,color-mix(in srgb,var(--kd-accent) 6%,transparent) 1px,transparent 1px);
            background-size:40px 40px; mask-image:radial-gradient(ellipse 70% 60% at 30% 40%,#000,transparent); }
        .kd-auth-main{ display:flex; align-items:center; justify-content:center; padding:32px 20px; }
        .kd-auth-card{ width:100%; max-width:380px; }
    </style>
</head>
<body>
    <aside class="kd-auth-aside">
        <div style="position:relative;display:flex;align-items:center;gap:11px;">
            <div style="height:40px;display:flex;align-items:center;padding:5px 12px;background:#fff;border-radius:var(--kd-radius-sm);">
                @if ($logo){{ $logo }}@else<span style="font-family:var(--kd-font-mono);font-weight:700;color:#0b0f14;">KD</span>@endif
            </div>
            <div><div style="font-weight:700;font-size:14px;">{{ $system }}</div><div style="font-size:12px;color:var(--kd-muted);">Ecosistema KraftDo</div></div>
        </div>
        <div style="position:relative;">
            {{ $aside ?? '' }}
            @unless (isset($aside))
                <div style="font-family:var(--kd-font-mono);font-size:12px;color:var(--kd-accent-text);letter-spacing:.08em;margin-bottom:14px;">ACCESO SEGURO</div>
                <div style="font-size:26px;font-weight:800;line-height:1.2;letter-spacing:-.02em;max-width:20ch;">Trámites de la ciudad, en un solo lugar.</div>
                <p style="font-size:13.5px;color:var(--kd-muted);line-height:1.6;max-width:36ch;margin-top:12px;">Tus datos están protegidos y no se comparten con terceros.</p>
            @endunless
        </div>
        <div style="position:relative;font-size:11.5px;color:var(--kd-hint);">© {{ date('Y') }} {{ $system }}</div>
    </aside>

    <main class="kd-auth-main">
        <div class="kd-auth-card">
            <div style="margin-bottom:24px;">
                <h1 style="margin:0;font-size:23px;font-weight:800;letter-spacing:-.02em;">{{ $title }}</h1>
                @if ($subtitle)<p style="margin:6px 0 0;font-size:13.5px;color:var(--kd-muted);line-height:1.5;">{{ $subtitle }}</p>@endif
            </div>
            {{ $slot }}
        </div>
    </main>
</body>
</html>

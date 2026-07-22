@props([
    'theme' => 'dark',
    'code' => '404',
    'title' => 'Página no encontrada',
    'message' => 'La página que buscas no existe o fue movida.',
    'home' => '/',
    'system' => 'Panel',
])

<!DOCTYPE html>
<html lang="es" data-kd-theme="{{ $theme }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $code }} · {{ $title }}</title>
    {{ $head ?? '' }}
    <style>
        *,*::before,*::after{ box-sizing:border-box; }
        body{ margin:0; min-height:100vh; display:grid; place-items:center; padding:24px; background:var(--kd-bg); color:var(--kd-text); font-family:var(--kd-font-sans); text-align:center; overflow:hidden; }
        .kd-err__grid{ position:fixed; inset:0; z-index:0; pointer-events:none; opacity:.6;
            background-image:linear-gradient(color-mix(in srgb,var(--kd-accent) 5%,transparent) 1px,transparent 1px),linear-gradient(90deg,color-mix(in srgb,var(--kd-accent) 5%,transparent) 1px,transparent 1px);
            background-size:46px 46px; mask-image:radial-gradient(ellipse 60% 50% at 50% 45%,#000,transparent); }
        .kd-err__card{ position:relative; z-index:1; max-width:440px; }
    </style>
</head>
<body>
    <div class="kd-err__grid"></div>
    <div class="kd-err__card">
        <div style="font-family:var(--kd-font-mono);font-size:clamp(80px,20vw,140px);font-weight:700;line-height:1;letter-spacing:-.04em;color:var(--kd-accent);text-shadow:var(--kd-glow);">{{ $code }}</div>
        <h1 style="margin:14px 0 0;font-size:22px;font-weight:800;letter-spacing:-.02em;">{{ $title }}</h1>
        <p style="margin:10px 0 26px;font-size:14px;color:var(--kd-muted);line-height:1.6;">{{ $message }}</p>
        <div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap;">
            @isset($actions)
                {{ $actions }}
            @else
                <x-kd::button :href="$home">Volver al inicio</x-kd::button>
            @endisset
        </div>
        <div style="margin-top:34px;font-family:var(--kd-font-mono);font-size:11px;color:var(--kd-hint);letter-spacing:.05em;">{{ $system }}</div>
    </div>
</body>
</html>

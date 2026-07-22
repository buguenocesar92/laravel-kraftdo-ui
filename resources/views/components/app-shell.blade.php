@props([
    'theme' => 'light',
    'title' => null,
    'system',
    'subtitle' => null,
    'status' => 'online',
    'maxWidth' => '1200px',
])

<!DOCTYPE html>
<html lang="es" data-muni-theme="{{ $theme }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? $system }}</title>
    {{ $head ?? '' }}
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body { margin: 0; background: var(--kd-bg); color: var(--kd-text); font-family: var(--kd-font-sans); }
        a { color: var(--kd-accent); }
    </style>
</head>
<body>
    <x-kd::topbar :system="$system" :subtitle="$subtitle" :status="$status">
        {{ $topbar ?? '' }}
    </x-kd::topbar>

    <main style="max-width:{{ $maxWidth }};margin:0 auto;padding:24px 16px 48px;">
        {{ $slot }}
    </main>
</body>
</html>

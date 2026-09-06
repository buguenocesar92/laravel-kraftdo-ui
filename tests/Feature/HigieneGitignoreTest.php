<?php

it('el .gitignore excluye los residuos del flujo con agentes', function () {
    // CCTG-35: un agente que trabaja en este repo deja detrás specs de
    // superpowers, worktrees de Claude Code, overrides de entorno (.envrc con
    // direnv) y capturas de Playwright — nada de eso es parte del paquete y
    // versionarlo por accidente ensucia el historial de un repo que además es
    // una librería consumida por 4 sistemas.
    $gitignore = (string) file_get_contents(__DIR__.'/../../.gitignore');

    $requeridos = [
        '.superpowers/',
        '.claude/worktrees/',
        '.envrc',
        '.direnv/',
        '/test-results',
    ];

    $faltantes = array_values(array_filter(
        $requeridos,
        fn (string $patron): bool => ! str_contains($gitignore, $patron)
    ));

    expect($faltantes)->toBe([]);
});

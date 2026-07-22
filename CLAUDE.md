# CLAUDE.md — laravel-kraftdo-ui

Sistema de diseño del ecosistema KraftDo: tokens de marca, 48 componentes Blade,
animaciones y el plugin de Filament. Lo consumen nfc-v2, sitio, crm y hub.

## Origen

**Este paquete es un fork de `muni-graneros/laravel-muni-ui`.** Se partió de él porque estaba
probado en tres sistemas municipales en producción y el 96 % de sus componentes usan tokens
semánticos, así que re-pintarlo fue cambiar variables. Si aparece un bug en un componente,
conviene mirar si el original ya lo arregló (y al revés).

Se eliminaron los 4 componentes del Gobierno de Chile (`gob-*`) y se sustituyeron los 9 tokens
de marca municipal por los de KraftDo.

## Reglas

- **Los componentes NUNCA usan tokens de marca** (`--kd-green`), solo semánticos
  (`--kd-accent`, `--kd-surface`, `--kd-text`). Hay un test que lo verifica.
- Las animaciones deben anularse bajo `prefers-reduced-motion`.
- Sin librerías JS externas: los renders NFC son snapshots self-contained servidos por Cloudflare.

## Paleta (valores del CSS de la landing kraftdo.cl)

`--kd-green #10b981` · `--kd-lime #32ff32` · `--kd-blue #3b82f6` ·
`--kd-navy #1e293b` · `--kd-dark #0f172a` · tipografía Inter.

## Desarrollo

    composer install
    vendor/bin/pest
    vendor/bin/pint

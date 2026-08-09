# Informe — cierre del hueco de contraste WCAG AA en tokens de texto

Rama: `fix/contraste-tokens-texto`

## 1. Comandos y salida — ANTES del fix (test ampliado, CSS sin tocar)

```
$ vendor/bin/pest tests/Feature/PaqueteTest.php
```

```
 FAIL  Tests\Feature\PaqueteTest
 ✓ it registra los componentes bajo el namespace kd                    0.32s
 ✓ it define los tokens de marca con los valores de la landing         0.02s
 ✓ it los componentes no usan tokens de marca directamente             0.02s
 ✓ it no quedan rastros de la identidad municipal                      0.02s
 ✓ it ningún par texto/fondo baja de WCAG AA                           0.02s
 ⨯ it ningún token usado como color de texto en los componentes baja d…  0.04s
 ✓ it las animaciones se anulan con prefers-reduced-motion              0.01s
────────────────────────────────────────────────────────────────────────────
 FAILED  Tests\Feature\PaqueteTest > it ningún token usado como color de t…
  Failed asserting that two arrays are identical.
-Array &0 []
+Array &0 [
+    0 => ':root: --kd-accent sobre --kd-bg = 2.35:1',
+    1 => ':root: --kd-accent sobre --kd-surface = 2.54:1',
+    2 => ':root: --kd-hint sobre --kd-bg = 4.47:1',
+    3 => ':root: --kd-warn-fg sobre --kd-bg = 3.16:1',
+    4 => ':root: --kd-warn-fg sobre --kd-surface = 3.42:1',
+    5 => ':root: --kd-accent-strong sobre --kd-bg = 3.48:1',
+    6 => ':root: --kd-accent-strong sobre --kd-surface = 3.77:1',
+    7 => '.dark: --kd-hint sobre --kd-bg = 2.56:1',
+    8 => '.dark: --kd-hint sobre --kd-surface = 2.09:1',
+    9 => '.dark: --kd-danger-fg sobre --kd-surface = 3.89:1',
+    10 => '.dark: --kd-info-fg sobre --kd-surface = 3.98:1',
+    11 => '.dark: --kd-accent-strong sobre --kd-surface = 3.88:1',
+    12 => '[data-kd-theme="light"]: --kd-accent sobre --kd-bg = 2.35:1',
+    13 => '[data-kd-theme="light"]: --kd-accent sobre --kd-surface = 2.54:1',
+    14 => '[data-kd-theme="light"]: --kd-hint sobre --kd-bg = 4.47:1',
+    15 => '[data-kd-theme="light"]: --kd-warn-fg sobre --kd-bg = 3.16:1',
+    16 => '[data-kd-theme="light"]: --kd-warn-fg sobre --kd-surface = 3.42:1',
+    17 => '[data-kd-theme="light"]: --kd-accent-strong sobre --kd-bg = 3.48:1',
+    18 => '[data-kd-theme="light"]: --kd-accent-strong sobre --kd-surface = 3.77:1',
+]

Tests:    1 failed, 6 passed (11 assertions)
```

19 fallas en vez de las 12 reportadas por César: 7 en `:root` + 5 en `.dark` +
las mismas 7 duplicadas en `[data-kd-theme="light"]` (ese bloque comparte
exactamente los mismos hex que `:root` light, así que reproduce las mismas 7
combinaciones — César no las había contado por separado, pero el test ahora
cubre el tercer selector como pedía la consigna).

## 2. Discrepancia en el conteo de "23 apariciones de --kd-accent"

Medí las apariciones reales de `color: var(--kd-accent)` (propiedad `color`
exacta, no `border-color`/`background-color`/`box-shadow`, con
`grep -P "(?<![a-zA-Z-])color:\s*var\(--kd-accent\b(?!-)"` sobre `.blade.php`
y `.css`) y encontré **12**, no 23. La cuenta de 23 sale de contar también
`border-color:` (que contiene la subcadena `color:`) cuando aparece en la
misma línea que un `color:` real — el ejemplo más claro es
`.kd-select:focus { border-color: var(--kd-accent); }`, que no tiene ninguna
`color:` real pero un grep sin límite de palabra la cuenta igual. Como la
consigna es explícita en "NO tocar border-color/background/fill/stroke/
box-shadow", usé el conteo preciso (12) y no el de 23. El detalle de los 12
usos y su archivo:línea está en el diff del commit.

## 3. Comandos y salida — DESPUÉS del fix

```
$ vendor/bin/pest tests/Feature/PaqueteTest.php
```

```
 PASS  Tests\Feature\PaqueteTest
 ✓ it registra los componentes bajo el namespace kd                    0.12s
 ✓ it define los tokens de marca con los valores de la landing         0.01s
 ✓ it los componentes no usan tokens de marca directamente             0.01s
 ✓ it no quedan rastros de la identidad municipal                      0.01s
 ✓ it ningún par texto/fondo baja de WCAG AA                           0.01s
 ✓ it ningún token usado como color de texto en los componentes baja d… 0.02s
 ✓ it las animaciones se anulan con prefers-reduced-motion              0.01s

Tests:    7 passed (11 assertions)
```

```
$ vendor/bin/pest
```
(mismo resultado: es toda la suite del paquete — no hay más archivos de test)
```
Tests:    7 passed (11 assertions)
```

```
$ vendor/bin/pint --test
{"tool":"pint","result":"passed"}
```

## 4. Verificación en vivo (navegador, no solo test unitario)

Serví el CSS real vía `php -S` + Playwright, renderizando los 6 tokens
corregidos (`--kd-accent-text`, `--kd-accent-strong`, `--kd-hint`,
`--kd-warn-fg`, `--kd-danger-fg`, `--kd-info-fg`) en light y en
`data-kd-theme="dark"`, y midiendo el contraste real con `getComputedStyle`
(no por captura, siguiendo el gotcha ya conocido de selectores de tema en
Filament v5). Resultado, coincide con lo calculado:

```
accent-text   | rgb(4,120,87)    sobre rgb(255,255,255) | 5.48
accent-strong | rgb(4,120,87)    sobre rgb(255,255,255) | 5.48
hint          | rgb(93,100,112)  sobre rgb(255,255,255) | 5.96
warn          | rgb(154,98,0)    sobre rgb(255,255,255) | 5.10
danger        | rgb(176,48,48)   sobre rgb(255,255,255) | 6.34  (sin cambios, light)
info          | rgb(31,95,176)   sobre rgb(255,255,255) | 6.32  (sin cambios, light)
accent-bg     | rgb(15,23,42)    sobre rgb(16,185,129)   | 7.04  (--kd-accent de fondo, intacto)

--- data-kd-theme="dark" ---
accent-text   | rgb(52,211,153)  sobre rgb(30,41,59)     | 7.61
accent-strong | rgb(52,211,153)  sobre rgb(30,41,59)     | 7.61
hint          | rgb(148,163,184) sobre rgb(30,41,59)     | 5.71
warn          | rgb(245,158,11)  sobre rgb(30,41,59)     | 6.81  (sin cambios, dark)
danger        | rgb(248,113,113) sobre rgb(30,41,59)     | 5.29
info          | rgb(96,165,250)  sobre rgb(30,41,59)     | 5.75
accent-bg     | rgb(15,23,42)    sobre rgb(16,185,129)   | 7.04  (--kd-accent de fondo, intacto)
```

Archivo temporal de prueba (`verificacion-contraste-tmp.html`) y servidor PHP
de verificación, borrados después de usarlos — no quedó nada suelto en el repo.

## 5. Qué se cambió

### `resources/css/kraftdo-ui.css`
- Nuevo token semántico `--kd-accent-text` (verde legible como texto) en los
  4 bloques de tema: `:root` (light), `@media (prefers-color-scheme: dark)`,
  `.dark`/`[data-kd-theme="dark"]`/`[data-theme="dark"]`, y
  `[data-kd-theme="light"]`.
  - Light: `#047857` (5.07:1 sobre bg, 5.48:1 sobre surface)
  - Dark: `#34d399` (9.29:1 sobre bg, 7.61:1 sobre surface)
- `--kd-accent` **no se tocó** (sigue siendo `var(--kd-green)` = `#10b981`):
  sigue funcionando para fondo/borde/relleno/box-shadow.
- Valores corregidos (mismo token, mismo nombre, solo el hex):
  - `--kd-accent-strong`: dejó de ser `var(--kd-green-dark)` y pasó a valor
    propio `#047857` (light) / `#34d399` (dark) — igual a `--kd-accent-text`
    porque ambos resuelven el mismo problema (verde como texto).
  - `--kd-hint`: `#6b7280`→`#5d6470` (light), `#4a5a74`→`#94a3b8` (dark)
  - `--kd-warn-fg`: `#c47a10`→`#9a6200` (solo light, dark ya pasaba)
  - `--kd-danger-fg`: `#ef4444`→`#f87171` (solo dark, light ya pasaba)
  - `--kd-info-fg`: dejó de ser `var(--kd-blue)` y pasó a `#60a5fa` (solo dark)
  - Verifiqué que `--kd-on-danger` sigue pasando AA contra el nuevo
    `--kd-danger-fg` (se usa como fondo sólido en el hover del botón de
    peligro): 6.45:1 en dark, 6.34:1 en light — ambos siguen arriba de 4.5.
- Apliqué las mismas correcciones también al bloque
  `@media (prefers-color-scheme: dark)` (el "dark automático" por preferencia
  de SO), aunque el test no lo cubre explícitamente: ese bloque duplica
  exactamente los valores de `.dark`, y dejarlo desactualizado habría dejado
  el bug vivo para cualquier usuario con el SO en oscuro y sin clase
  explícita. No estaba en la consigna pero era la misma corrección aplicada
  dos veces, así que la hice.

### 12 usos reales de `color: var(--kd-accent)` → `--kd-accent-text`
9 en componentes Blade (`color: var(--kd-accent-text)`):
`calendar.blade.php`, `stepper.blade.php`, `file-dropzone.blade.php`,
`tabs.blade.php`, `page-header.blade.php`, `app-shell.blade.php`,
`nav-item.blade.php`, `auth-shell.blade.php`, `error-page.blade.php`.

3 en `resources/css/kraftdo-ui-filament.css` (barra lateral de Filament):
**estos NO usan `var(--kd-accent-text)`**, sino el hex fijo `#34d399`
(el valor dark), con comentario explicando el motivo. Ese archivo documenta
en su cabecera que la barra lateral es "SIEMPRE oscura, independiente del
toggle claro/oscuro" y por eso usa valores fijos en vez de tokens que
cambian con el tema — si hubiera usado `var(--kd-accent-text)`, en modo
claro habría resuelto al verde OSCURO pensado para fondo blanco (`#047857`),
ilegible sobre la barra lateral que siempre es oscura. Encontrado leyendo el
comentario de cabecera del archivo antes de hacer el reemplazo mecánico —
si el subagente hubiera hecho un search-and-replace ciego habría introducido
una regresión de contraste.

Ningún uso de `--kd-accent` como `background`/`border-color`/`box-shadow`
se tocó (verificado con grep tras el cambio: no queda ningún
`color: var(--kd-accent)` genuino en el repo).

### `tests/Pest.php`
No hizo falta arreglar `kdToken`: ya fusiona los dos bloques `:root` (el de
marca y el de valores light) y ya resuelve una capa de indirección `var()`
contra todo el CSS. Lo comprobé antes de tocar nada.

### `tests/Feature/PaqueteTest.php`
- El test de 4 pares fijos ahora también recorre `[data-kd-theme="light"]`
  (antes solo `:root` y `.dark`).
- Test nuevo: deriva los tokens usados como `color:` (propiedad exacta, no
  `border-color`/`background-color`) desde los 48 componentes Blade + el CSS
  de Filament con la regex
  `(?<![a-zA-Z-])color:\s*var\(\s*(--kd-[a-z-]+)\s*\)`, y mide cada uno
  contra `--kd-bg` y `--kd-surface` en los 3 temas.
  - Excluye `--kd-on-accent` y `--kd-on-danger`: son texto para un relleno
    sólido específico (botón primario, botón de peligro en hover), no para
    el fondo de página — ya los cubre el test de pares contra su propio
    fondo. Medirlos contra `--kd-bg`/`--kd-surface` daría un falso positivo
    (en dark, `--kd-on-accent` vale igual que `--kd-bg`: 1:1 consigo mismo,
    un patrón que nunca se renderiza así).
  - Excluye `--kd-bg` (usado una vez en el tooltip, patrón invertido: texto
    claro sobre relleno oscuro que es `--kd-text`, no la página) y
    `--kd-border-2` (relleno de un ícono decorativo, la estrella sin marcar
    del rating) por el mismo motivo: no se renderizan sobre
    `--kd-bg`/`--kd-surface`.

## 6. Test hermano ("los componentes no usan tokens de marca directamente")

Sigue pasando. `--kd-accent-text` es semántico (no está en la lista
`green|lime|blue|navy|dark` que ese test prohíbe), y ningún componente quedó
referenciando un token de marca directo.

## 7. Qué NO se arregló / quedó fuera de alcance

- El decorativo `--kd-border-2` como relleno de ícono (estrella sin marcar en
  `rating.blade.php`) y `--kd-bg` como texto invertido en el tooltip miden
  <4.5:1 contra `--kd-bg`/`--kd-surface` si se los mide ahí, pero ninguno se
  renderiza realmente sobre esos fondos (el primero es decorativo, el
  segundo va sobre `--kd-text` como fondo). Los excluí del barrido con
  comentario explicando el motivo en vez de "arreglarlos" contra un fondo
  que no aplica. Si en algún momento se usan de otra forma, alguien va a
  tener que revisar esta exclusión.
- No toqué `--kd-info-fg`/`--kd-warn-fg`/`--kd-ok-fg`/`--kd-danger-fg` como
  texto sobre su propio fondo tintado (`--kd-info-bg`, etc., patrón de
  badge/alert) — no estaba en la lista de fallas de César y no lo medí; si
  hace falta, es un test aparte con sus propios pares.

---

## 8. Ronda 2 — corrección de la regresión que introdujo la ronda 1

Una revisión posterior encontró que el fix de la ronda 1 rompió el hover del
botón primario: `--kd-accent-strong` sirve DOS roles opuestos (fondo del
hover y, en 2 sitios, texto) y la ronda 1 le dio el valor pensado solo para
el rol de texto. Corregido en esta ronda. Detalle completo abajo.

### 8.1 CICLO TDD — cobertura del hover ANTES del fix (falla)

Agregué el par `['--kd-on-accent', '--kd-accent-strong']` al test de pares
fijos (es el hover real de `.kd-btn--primary:hover`) y corrí solo ese test
contra el CSS sin tocar:

```
$ vendor/bin/pest --filter="ningún par texto/fondo"

 FAIL  Tests\Feature\PaqueteTest
 ⨯ it ningún par texto/fondo baja de WCAG AA   0.12s
────────────────────────────────────────────────────────────────
Failed asserting that two arrays are identical.
-Array &0 []
+Array &0 [
+    0 => ':root: --kd-on-accent sobre --kd-accent-strong = 3.26:1',
+    1 => '[data-kd-theme="light"]: --kd-on-accent sobre --kd-accent-strong = 3.26:1',
+]
Tests:    1 failed (1 assertions)
```

Confirma exactamente el 3.26:1 reportado en la revisión, en los dos temas
claros (`:root` y `[data-kd-theme="light"]`, que comparten valores). `.dark`
no aparece: ahí `--kd-accent-strong` seguía en `#34d399`, que sí cumplía.

### 8.2 Corrección aplicada

**`resources/css/kraftdo-ui.css`** — separé los roles de `--kd-accent-strong`
(FONDO) y `--kd-accent-text` (TEXTO), que hasta ahora compartían el mismo hex
en claro por error:

- `--kd-accent-strong` en los 2 bloques claros (`:root`, `[data-kd-theme="light"]`):
  `#047857` → `#059669` (`var(--kd-green-dark)`). Con `--kd-on-accent`
  (`#0f172a`) encima da **4.74:1** (antes 3.26:1).
- `--kd-accent-text` queda intacto en `#047857` (su rol de texto: 5.07:1
  sobre bg, 5.48:1 sobre surface, ya verificado en la ronda 1).
- En oscuro (`@media (prefers-color-scheme: dark)` y `.dark`/`[data-kd-theme="dark"]`)
  **no cambié el valor**: `--kd-accent-strong` ya estaba en `#34d399`, y medido
  como fondo con `--kd-on-accent` encima da 9.29:1 — cumple. Lo verifiqué en
  vez de asumir que "como el bug fue solo en claro, dark está bien": medí el
  número antes de decidir no tocarlo.
- Agregué comentarios en la definición de cada token en los 4 bloques
  explicando el rol (FONDO vs TEXTO) y por qué no hay que reutilizar el valor
  del otro rol sin volver a medir — es la causa de fondo de este bug.

**`resources/css/kraftdo-ui-filament.css`** — los 2 usos de
`--kd-accent-strong` como `color:` (texto) pasaron a `--kd-accent-text`:
`.fi-header-heading` (línea 81) y `.fi-wi-stats-overview-stat-value` (línea
93), cada uno con comentario explicando el motivo. Antes de este cambio, con
la regex del punto 8.3 ya arreglada, estos dos usos habrían hecho que
`--kd-accent-strong` (ahora `#059669`, valor de FONDO) entrara al barrido
automático de texto y fallara contra `--kd-bg`/`--kd-surface` (3.48:1/3.77:1)
— confirmando que el cambio era necesario, no cosmético.

### 8.3 Regex del barrido automático no veía tokens con dígitos

`tests/Feature/PaqueteTest.php`, test "ningún token usado como color de
texto…": la clase de caracteres era `[a-z-]+` (sin dígitos). Verificado:
`--kd-border-2` (usado como `color:` en el ícono decorativo de
`rating.blade.php`) nunca hacía match, así que el `unset($tokens['--kd-border-2'])`
de la línea de abajo era un no-op — la clave nunca se poblaba para empezar.
Cambié la clase a `[a-z0-9-]+` y dejé un comentario explicando el defecto
anterior. Repasé el barrido resultante a mano (`--kd-border-2` es el único
token con dígito que aparece como `color:` en todo el repo) y comprobé que,
medido como si fuera texto, da <2:1 contra `--kd-bg`/`--kd-surface` en los
tres temas — el `unset` sigue siendo necesario (ahora sí hace algo) y
actualicé su comentario para decir por qué, con el número real.

### 8.4 Otros fondos dinámicos con texto propio no medidos

Revisé todos los `background: var(--kd-...)` en reglas `:hover`/`:active`/
`:focus` de los 48 componentes + el CSS de Filament, buscando cuáles fijan
también su propio `color:` (el mismo patrón que causó el bug: texto+fondo
que solo coexisten en un estado dinámico, invisibles para el test que solo
mide contra `--kd-bg`/`--kd-surface` estáticos). Encontré, además del ya
cubierto `.kd-btn--danger:hover` (`--kd-on-danger`/`--kd-danger-fg`, ya en la
lista de pares desde la ronda 1):

- `--kd-text` sobre `--kd-surface-2`: `.kd-btn--ghost:hover`,
  `.kd-nav-item:hover`, `.kd-page:hover` (paginación).
- `--kd-text` sobre `--kd-surface-3`: `.kd-btn--subtle:hover`,
  `.kd-drawer__x:hover`, `.kd-modal-x:hover`.

Agregué ambos pares a la lista de pares fijos. Los dos pasan cómodo en los
tres temas (14.5:1–15.6:1 en claro, 7.2:1–9.9:1 en oscuro) — no eran un bug,
pero quedaban sin cobertura por el mismo hueco estructural, así que los até
para que un cambio futuro de `--kd-surface-2`/`--kd-surface-3` no repita la
historia.

### 8.5 Hex fijos `#34d399` sin vigilancia (menor)

Agregué un test barato que ata los 3 hex fijos de la barra lateral de
Filament (defendibles: la barra es siempre oscura, no sigue el toggle) al
valor real de `--kd-accent-text` en `.dark`. Si alguno de los dos cambia sin
el otro, el test falla en vez de quedar como una deriva visual silenciosa.
No até el `#34d399` que aparece solo en un comentario (línea 33) — el test
cuenta declaraciones `color:#34d399`, que son exactamente 3.

### 8.6 Comandos y salida — DESPUÉS del fix

```
$ vendor/bin/pest

 PASS  Tests\Feature\PaqueteTest
 ✓ it registra los componentes bajo el namespace kd
 ✓ it define los tokens de marca con los valores de la landing
 ✓ it los componentes no usan tokens de marca directamente
 ✓ it no quedan rastros de la identidad municipal
 ✓ it ningún par texto/fondo baja de WCAG AA
 ✓ it ningún token usado como color de texto en los componentes baja de WCAG AA
 ✓ it las animaciones se anulan con prefers-reduced-motion
 ✓ it el verde fijo de la barra lateral de Filament sigue al --kd-accent-text oscuro

Tests:    8 passed (13 assertions)
```

```
$ vendor/bin/pint
{"tool":"pint","result":"passed"}
```

### 8.7 Verificación en vivo (hover real, no captura)

Serví el paquete con `php -S` + Playwright (`file://` está bloqueado para
navegación en este entorno, así que serví el repo por HTTP local). Rendericé
un `.kd-btn--primary` en 3 contextos (`:root` light por defecto, `data-kd-theme="dark"`,
`data-kd-theme="light"` forzado), hice `hover()` real sobre cada botón
(dispara el `:hover` real del navegador, no una simulación) y leí
`getComputedStyle(el).backgroundColor`/`.color` ya en estado hover:

```
light (default):        bg rgb(5,150,105)   / color rgb(15,23,42)   → 4.74:1
dark (data-kd-theme):    bg rgb(52,211,153)  / color rgb(15,23,42)   → 9.29:1
light forzado:           bg rgb(5,150,105)   / color rgb(15,23,42)   → 4.74:1
```

Los tres pasan AA. Coincide exactamente con lo calculado a mano y con el
test. Archivo temporal y servidor PHP, ambos borrados al terminar — no quedó
nada suelto en el repo (`git status` limpio salvo los archivos tocados).

### 8.8 Qué quedó sin resolver

Nada de lo pedido en esta ronda. Puntos abiertos ya conocidos de la ronda 1
(sección 7) siguen igual: `--kd-border-2` e `--kd-info-fg`/`--kd-warn-fg`/
`--kd-ok-fg`/`--kd-danger-fg` como texto sobre su propio fondo tintado
(badge/alert) no se midieron — no estaban en el alcance de ninguna de las
dos rondas.

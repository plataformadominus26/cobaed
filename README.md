# Plataforma COBAED

Código compartido por los planteles **Lomas** (cobaedlomas.com) y **Villas** (cobaedvillas.com).

## Configuración por plantel
Todo lo que cambia entre planteles vive en `config.local.php`. Ese archivo **no se sube a git**.
Para instalar un plantel nuevo, copia `config.local.example.php` como `config.local.php` y llena los datos
de la base de datos, el nombre del plantel y la llave de Gemini.

Reglas:
- No escribas en el código nombres de plantel ni dominios. Usa `cfg('...')`, `plantel_nombre()`,
  `$_SERVER['HTTP_HOST']` o `location.host`.
- No subas datos de alumnos, fotos, logs ni credenciales (ver `.gitignore`).

## Flujo de trabajo
1. Los cambios se hacen y se prueban en **Villas**, luego `git commit` + `git push`.
2. Los demás planteles se actualizan **solos cada 5 minutos** (`deploy/actualizar.sh` en cron).
   Si un archivo tiene error de sintaxis o alguien modificó el servidor a mano, la actualización
   se detiene y lo anota en `~/logs/cobaed_actualizar.log` del plantel.
3. Cambios a la base de datos: un archivo nuevo en `deploy/migraciones/` (ver su README).
   Cada plantel lo aplica solo al actualizarse.

## Agregar un plantel nuevo
1. En el panel del servidor crea el sitio y la base de datos (vacía) del plantel.
2. Con el usuario del plantel, copia `deploy/nuevo_plantel.sh` y ejecútalo: `bash nuevo_plantel.sh`.
3. Te pedirá el nombre, la carpeta, los datos de la base y te mostrará una llave para agregarla en
   GitHub (Settings → Deploy keys, sin permiso de escritura). Al terminar muestra el usuario de acceso.

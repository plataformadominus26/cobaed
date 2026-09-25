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
1. Los cambios se hacen y se prueban en Villas, luego `git commit` + `git push`.
2. Cada plantel se actualiza con `git pull`.

# Migraciones de base de datos

Cuando un cambio necesite modificar la base (nueva tabla, columna, catálogo), agrega aquí un archivo
`.sql` nuevo cuyo nombre empiece con la fecha, por ejemplo `20261001_agrega_columna_curp.sql`.

- Cada plantel lo aplica solo, una única vez, al actualizarse (`deploy/migrar.php`).
- No edites una migración ya publicada; si hay que corregir algo, crea otra.
- Solo cambios de estructura o catálogos compartidos, nunca datos de alumnos o personal de un plantel.
- Actualiza también `deploy/esquema.sql` (lo usan los planteles nuevos).

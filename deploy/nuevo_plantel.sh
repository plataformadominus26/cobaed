#!/usr/bin/env bash
# Instala la plataforma COBAED para un plantel nuevo.
# Ejecutar en el servidor, con el usuario del plantel:   bash nuevo_plantel.sh
# (se puede descargar con: curl -O https://raw.githubusercontent.com/... o copiarlo desde otro plantel)
set -euo pipefail
REPO="plataformadominus26/cobaed"

echo "=== Instalación de la plataforma COBAED ==="
read -rp "Nombre del plantel como se verá en pantalla (ej. Cobaed Forestal): " NOMBRE
read -rp "Carpeta web donde se instalará (ej. $HOME/htdocs/cobaedforestal.com/cobaed): " DESTINO
read -rp "Base de datos (ya creada en el panel) - nombre: " DBNAME
read -rp "Base de datos - usuario: " DBUSER
read -rsp "Base de datos - contraseña: " DBPWD; echo
read -rp "Usuario para el primer acceso al sistema (ej. sistemas): " ADMINUSR

if [ -d "$DESTINO" ] && [ -n "$(ls -A "$DESTINO")" ]; then
    echo "La carpeta $DESTINO ya tiene archivos. No se instala nada."; exit 1
fi

# 1. Llave para descargar de GitHub
KEY="$HOME/.ssh/github_cobaed"
mkdir -p "$HOME/.ssh"; chmod 700 "$HOME/.ssh"
[ -f "$KEY" ] || ssh-keygen -t ed25519 -N "" -C "$(whoami)-deploy" -f "$KEY" -q
grep -q "^Host github-cobaed" "$HOME/.ssh/config" 2>/dev/null || \
    printf '\nHost github-cobaed\n    HostName github.com\n    User git\n    IdentityFile %s\n    IdentitiesOnly yes\n' "$KEY" >> "$HOME/.ssh/config"
chmod 600 "$HOME/.ssh/config"
export GIT_SSH_COMMAND="ssh -o StrictHostKeyChecking=accept-new -o BatchMode=yes"
until git ls-remote -q "github-cobaed:$REPO.git" >/dev/null 2>&1; do
    echo
    echo "Agrega esta llave en https://github.com/$REPO/settings/keys"
    echo "(Add deploy key, SIN marcar 'Allow write access'):"
    echo; cat "$KEY.pub"; echo
    read -rp "Presiona Enter cuando la hayas agregado... " _
done

# 2. Código (el historial de git queda fuera de la carpeta web)
mkdir -p "$HOME/git" "$DESTINO"
git clone -q --separate-git-dir="$HOME/git/cobaed.git" "github-cobaed:$REPO.git" "$DESTINO"
cd "$DESTINO"
git config core.fileMode false
echo "Código descargado en $DESTINO"

# 3. Configuración propia del plantel
php -r '
[$_, $nombre, $name, $user, $pwd] = $argv;
$c = ["plantel" => ["nombre" => $nombre, "nombre_largo" => $nombre],
      "db" => ["host" => "localhost", "user" => $user, "pwd" => $pwd, "name" => $name],
      "db_sebised_pwd" => "", "gemini_api_key" => ""];
file_put_contents("config.local.php", "<?php\n// Configuración propia de ESTE plantel. No se sube a git.\nreturn " . var_export($c, true) . ";\n");
' "$NOMBRE" "$DBNAME" "$DBUSER" "$DBPWD"
chmod 640 config.local.php

# 4. Base de datos: tablas y catálogos
php deploy/migrar.php --instalar

# 5. Primer usuario (directivo) para entrar al sistema
ADMINPWD=$(php -r 'echo random_int(100000, 999999);')
php -r '
require "config.php";
$db = new mysqli(cfg("db.host"), cfg("db.user"), cfg("db.pwd"), cfg("db.name"));
$db->set_charset("utf8mb4");
$st = $db->prepare("INSERT INTO usuarios SET token=?, book_id=1, empresa_id=0, nombre=?, celular=\"\", calle=\"\", colonia_id=0, puesto_id=6, usuario=\"\", pwd=?, rems=\"\", email=?, inicio_id=0, nivel=6, cp=0, activo=1, dpto_id=0, horario_id=0, ingreso=CURDATE(), imei=\"\"");
$tok = bin2hex(random_bytes(4)); $nom = "SISTEMAS " . mb_strtoupper($argv[1]);
$st->bind_param("ssss", $tok, $nom, $argv[3], $argv[2]);
$st->execute();
' "$NOMBRE" "$ADMINUSR" "$ADMINPWD"

# 6. Actualización automática cada 5 minutos
( crontab -l 2>/dev/null | grep -v "deploy/actualizar.sh" || true
  echo "*/5 * * * * bash $DESTINO/deploy/actualizar.sh" ) | crontab -

echo
echo "=== Listo ==="
echo "Plantel:     $NOMBRE"
echo "Acceso:      usuario '$ADMINUSR'  contraseña '$ADMINPWD'  (cámbiala al entrar)"
echo "Se actualiza solo cada 5 minutos. Registro: ~/logs/cobaed_actualizar.log"

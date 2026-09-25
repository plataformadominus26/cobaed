#!/usr/bin/env bash
# Actualiza este plantel desde GitHub (rama main). Lo ejecuta cron cada 5 minutos.
# Registro: ~/logs/cobaed_actualizar.log
set -u
DIR="$(cd "$(dirname "$0")/.." && pwd)"
LOG="$HOME/logs/cobaed_actualizar.log"
mkdir -p "$(dirname "$LOG")"
log() { echo "$(date '+%F %T') $*" >> "$LOG"; }

# Evita dos ejecuciones al mismo tiempo
exec 9>"$HOME/.cobaed_actualizar.lock"
flock -n 9 || exit 0

cd "$DIR" || { log "ERROR: no existe $DIR"; exit 1; }
timeout 60 git fetch -q origin main 2>>"$LOG" || { log "ERROR: no se pudo conectar con GitHub"; exit 1; }

LOCAL=$(git rev-parse HEAD)
REMOTO=$(git rev-parse origin/main)
[ "$LOCAL" = "$REMOTO" ] && exit 0

# No pisar cambios hechos a mano en este servidor
if ! git diff --quiet || ! git diff --cached --quiet; then
    log "ALTO: hay archivos modificados a mano en $DIR (git status); no se actualiza"
    exit 1
fi
if ! git merge-base --is-ancestor "$LOCAL" "$REMOTO"; then
    log "ALTO: este servidor tiene commits que no están en GitHub; no se actualiza"
    exit 1
fi

# Revisar la sintaxis PHP de la versión nueva antes de aplicarla
for f in $(git diff --name-only --diff-filter=AM "$LOCAL" "$REMOTO" -- '*.php'); do
    if ! git show "$REMOTO:$f" | php -l >/dev/null 2>&1; then
        log "ALTO: error de sintaxis en $f (${REMOTO:0:7}); no se actualiza"
        exit 1
    fi
done

# Llaves que la versión nueva deja de incluir: guardarlas en ~/secure antes de que git las borre
mkdir -p "$HOME/secure" && chmod 700 "$HOME/secure"
for f in $(git diff --name-only --diff-filter=D "$LOCAL" "$REMOTO" -- '*.key' '*.secret' '*service-account*.json' '*firebase-adminsdk*.json'); do
    if [ -f "$f" ] && [ ! -e "$HOME/secure/$(basename "$f")" ]; then
        cp -p "$f" "$HOME/secure/$(basename "$f")" && chmod 600 "$HOME/secure/$(basename "$f")"
        log "Llave rescatada: $f -> ~/secure/$(basename "$f")"
    fi
done

if ! git merge -q --ff-only "$REMOTO" 2>>"$LOG"; then
    log "ERROR: falló la actualización a ${REMOTO:0:7}"
    exit 1
fi
log "Actualizado ${LOCAL:0:7} -> ${REMOTO:0:7}: $(git log -1 --format=%s)"

php "$DIR/deploy/migrar.php" >> "$LOG" 2>&1 || log "ERROR: falló una migración de base de datos"

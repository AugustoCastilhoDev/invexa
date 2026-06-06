#!/bin/bash
# =============================================================================
# Invexa — Script de Backup Automatizado
# Caminho no VPS: /usr/local/bin/invexa-backup.sh
# Crontab:  0 3 * * * /usr/local/bin/invexa-backup.sh >> /var/log/invexa-backup.log 2>&1
# =============================================================================

set -euo pipefail

# ── Configurações — ajustar conforme ambiente
DATE=$(date +%Y%m%d_%H%M%S)
DB_NAME="invexa"
DB_USER="root"
# Defina DB_PASSWORD como variável de ambiente no VPS ou use um arquivo ~/.my.cnf
DB_PASSWORD="${DB_PASSWORD:-}"
BACKUP_DIR="/var/backups/invexa"
RETENTION_LOCAL_DAYS=7
RETENTION_REMOTE_DAYS=30
# Nome do remote configurado no rclone (ex: "b2" para Backblaze ou "s3" para AWS)
RCLONE_REMOTE="b2"
RCLONE_BUCKET="invexa-backups"
ALERT_EMAIL="${ALERT_EMAIL:-suporte@invexa.com.br}"

# ── Funções
log() { echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1"; }

alert_failure() {
    local msg="$1"
    log "ERRO: $msg"
    echo "Invexa Backup FALHOU em $(hostname) — $msg" \
        | mail -s "[INVEXA] Falha no backup" "$ALERT_EMAIL" 2>/dev/null || true
    exit 1
}

# ── Cria diretório de backup
mkdir -p "$BACKUP_DIR" || alert_failure "Não foi possível criar $BACKUP_DIR"

FILE_NAME="invexa_${DATE}.sql.gz"
FILE_PATH="$BACKUP_DIR/$FILE_NAME"

# ── Dump do banco
log "Iniciando dump do banco '$DB_NAME'..."
if [ -n "$DB_PASSWORD" ]; then
    mysqldump -u "$DB_USER" -p"$DB_PASSWORD" \
        --single-transaction \
        --routines \
        --triggers \
        "$DB_NAME" | gzip > "$FILE_PATH" || alert_failure "mysqldump falhou"
else
    # Usa ~/.my.cnf para credenciais (mais seguro)
    mysqldump -u "$DB_USER" \
        --single-transaction \
        --routines \
        --triggers \
        "$DB_NAME" | gzip > "$FILE_PATH" || alert_failure "mysqldump falhou"
fi

FILE_SIZE=$(du -sh "$FILE_PATH" | cut -f1)
log "Dump concluído: $FILE_NAME ($FILE_SIZE)"

# ── Upload para armazenamento externo via rclone
if command -v rclone &>/dev/null; then
    log "Enviando para $RCLONE_REMOTE:$RCLONE_BUCKET..."
    rclone copy "$FILE_PATH" "${RCLONE_REMOTE}:${RCLONE_BUCKET}/db/" || alert_failure "Upload rclone falhou"
    log "Upload concluído."

    # Remover backups remotos com mais de RETENTION_REMOTE_DAYS dias
    rclone delete --min-age "${RETENTION_REMOTE_DAYS}d" "${RCLONE_REMOTE}:${RCLONE_BUCKET}/db/" 2>/dev/null || true
else
    log "AVISO: rclone não encontrado. Backup armazenado apenas localmente."
fi

# ── Remover backups locais antigos
find "$BACKUP_DIR" -name "*.sql.gz" -mtime +"$RETENTION_LOCAL_DAYS" -delete
log "Backups locais com mais de ${RETENTION_LOCAL_DAYS} dias removidos."

log "Backup finalizado com sucesso: $FILE_NAME"

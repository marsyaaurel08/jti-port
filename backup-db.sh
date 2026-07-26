#!/bin/bash
set -euo pipefail

CONTAINER_NAME="jtiport_mysql"
DB_NAME="jtiport"
DB_USER="jtiport_user"
DB_PASS="jtiport_root_password"
BACKUP_DIR="$HOME/backups/sql"
DATE=$(date +%Y-%m-%d_%H-%M-%S)
RETENTION_DAYS=7

mkdir -p "$BACKUP_DIR"

# Cek container jalan sebelum dump
if ! docker ps --format '{{.Names}}' | grep -q "^${CONTAINER_NAME}$"; then
    echo "ERROR: Container $CONTAINER_NAME tidak ditemukan atau tidak jalan." >&2
    exit 1
fi

docker exec "$CONTAINER_NAME" mariadb-dump -u"$DB_USER" -p"$DB_PASS" "$DB_NAME" \
  | gzip > "$BACKUP_DIR/backup_${DB_NAME}_${DATE}.sql.gz"

find "$BACKUP_DIR" -type f -name "*.sql.gz" -mtime +$RETENTION_DAYS -delete

echo "Backup selesai: backup_${DB_NAME}_${DATE}.sql.gz"
#!/bin/bash
# ==============================================================================
# NCTB AI Learning Hub — Safe Database Restore Script
# Usage: bash scripts/restore_db.sh <backup_file.sql.gz|backup_file.sql>
# ==============================================================================

set -euo pipefail

if [ $# -lt 1 ]; then
    echo "Usage: bash scripts/restore_db.sh <path_to_backup_file>"
    exit 1
fi

BACKUP_FILE="$1"

if [ ! -f "${BACKUP_FILE}" ]; then
    echo "❌ Error: Backup file '${BACKUP_FILE}' does not exist."
    exit 1
fi

read -p "⚠️ WARNING: This will overwrite the current database with '${BACKUP_FILE}'. Continue? [y/N] " confirm
if [[ ! "${confirm}" =~ ^[Yy]$ ]]; then
    echo "Restore cancelled."
    exit 0
fi

echo "🔄 Restoring database..."

if [[ "${BACKUP_FILE}" == *.gz ]]; then
    DECOMPRESS_CMD="gunzip -c"
else
    DECOMPRESS_CMD="cat"
fi

if docker ps --format '{{.Names}}' 2>/dev/null | grep -q "^nctb-mysql$"; then
    echo "🐳 Importing into Docker container 'nctb-mysql'..."
    ${DECOMPRESS_CMD} "${BACKUP_FILE}" | docker exec -i nctb-mysql mysql -u wordpress -pwordpress wordpress
elif command -v mysql >/dev/null 2>&1; then
    echo "💾 Importing via host mysql client..."
    ${DECOMPRESS_CMD} "${BACKUP_FILE}" | mysql -u "${DB_USER:-wordpress}" -p"${DB_PASSWORD:-wordpress}" -h "${DB_HOST:-localhost}" "${DB_NAME:-wordpress}"
else
    echo "❌ Error: Neither Docker nor mysql client is available to restore the database."
    exit 1
fi

echo "✅ Database restored successfully from: ${BACKUP_FILE}"

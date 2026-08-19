#!/bin/bash
# ==============================================================================
# NCTB AI Learning Hub — Automated Database Backup Script
# Usage: bash scripts/backup_db.sh [backup_dir]
# ==============================================================================

set -euo pipefail

BACKUP_DIR="${1:-./backups/db}"
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
BACKUP_FILE="${BACKUP_DIR}/nctb_backup_${TIMESTAMP}.sql.gz"
RETENTION_DAYS=7

mkdir -p "${BACKUP_DIR}"

echo "📦 Starting NCTB database backup..."

# Detect Docker container or local MySQL
if docker ps --format '{{.Names}}' 2>/dev/null | grep -q "^nctb-mysql$"; then
    echo "🐳 Exporting from Docker container 'nctb-mysql'..."
    docker exec nctb-mysql mysqldump -u wordpress -pwordpress wordpress | gzip > "${BACKUP_FILE}"
elif command -v mysqldump >/dev/null 2>&1; then
    echo "💾 Exporting via host mysqldump..."
    mysqldump -u "${DB_USER:-wordpress}" -p"${DB_PASSWORD:-wordpress}" -h "${DB_HOST:-localhost}" "${DB_NAME:-wordpress}" | gzip > "${BACKUP_FILE}"
else
    echo "❌ Error: Neither Docker nor mysqldump is available to create a database dump."
    exit 1
fi

echo "✅ Backup successfully created at: ${BACKUP_FILE} ($(du -h "${BACKUP_FILE}" | cut -f1))"

# Prune backups older than retention days
find "${BACKUP_DIR}" -name "nctb_backup_*.sql.gz" -mtime +"${RETENTION_DAYS}" -delete 2>/dev/null || true
echo "🧹 Pruned backups older than ${RETENTION_DAYS} days."

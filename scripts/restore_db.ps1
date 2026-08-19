# ==============================================================================
# NCTB AI Learning Hub — Safe Database Restore Script (PowerShell)
# Usage: .\scripts\restore_db.ps1 -BackupFile .\backups\db\nctb_backup_xxxx.sql
# ==============================================================================
param (
    [Parameter(Mandatory=$true)]
    [string]$BackupFile
)

$ErrorActionPreference = "Stop"

if (-not (Test-Path $BackupFile)) {
    Write-Host "❌ Error: Backup file '$BackupFile' does not exist." -ForegroundColor Red
    exit 1
}

$confirm = Read-Host "⚠️ WARNING: This will overwrite the current database with '$BackupFile'. Type 'YES' to proceed"
if ($confirm -ne "YES") {
    Write-Host "Restore cancelled." -ForegroundColor Yellow
    exit 0
}

Write-Host "🔄 Restoring database..." -ForegroundColor Cyan

# Check if Docker container is running
$dockerRunning = $false
try {
    $containers = docker ps --format "{{.Names}}" 2>$null
    if ($containers -match "nctb-mysql") {
        $dockerRunning = $true
    }
} catch {}

if ($dockerRunning) {
    Write-Host "🐳 Importing into Docker container 'nctb-mysql'..." -ForegroundColor Green
    Get-Content $BackupFile | docker exec -i nctb-mysql mysql -u wordpress -pwordpress wordpress
} else {
    Write-Host "💾 Importing via local mysql client..." -ForegroundColor Yellow
    Get-Content $BackupFile | mysql -u wordpress -pwordpress wordpress
}

Write-Host "✅ Database restored successfully from: $BackupFile" -ForegroundColor Green

# ==============================================================================
# NCTB AI Learning Hub — Automated Database Backup Script (PowerShell)
# Usage: .\scripts\backup_db.ps1 [-BackupDir .\backups\db]
# ==============================================================================
param (
    [string]$BackupDir = ".\backups\db"
)

$ErrorActionPreference = "Stop"

if (-not (Test-Path $BackupDir)) {
    New-Item -ItemType Directory -Path $BackupDir -Force | Out-Null
}

$Timestamp = Get-Date -Format "yyyyMMdd_HHmmss"
$BackupFile = Join-Path $BackupDir "nctb_backup_$Timestamp.sql"

Write-Host "📦 Starting NCTB database backup..." -ForegroundColor Cyan

# Check if Docker container is running
$dockerRunning = $false
try {
    $containers = docker ps --format "{{.Names}}" 2>$null
    if ($containers -match "nctb-mysql") {
        $dockerRunning = $true
    }
} catch {}

if ($dockerRunning) {
    Write-Host "🐳 Exporting from Docker container 'nctb-mysql'..." -ForegroundColor Green
    docker exec nctb-mysql mysqldump -u wordpress -pwordpress wordpress > $BackupFile
} else {
    Write-Host "💾 Attempting export via local mysqldump..." -ForegroundColor Yellow
    mysqldump -u wordpress -pwordpress wordpress > $BackupFile
}

if (Test-Path $BackupFile) {
    $size = (Get-Item $BackupFile).Length / 1KB
    Write-Host ("✅ Backup successfully created at: {0} ({1:N1} KB)" -f $BackupFile, $size) -ForegroundColor Green
} else {
    Write-Host "❌ Failed to create backup file." -ForegroundColor Red
}

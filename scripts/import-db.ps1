<#
.SYNOPSIS
    Mengganti isi database MariaDB Docker lokal dengan file dump SQL (mis. export phpMyAdmin produksi).

.DESCRIPTION
    Database lokal di service "db" dikosongkan (DROP + CREATE), dump diimpor, lalu (opsional)
    migration dijalankan seperti saat deploy. Hanya menyentuh database Docker lokal.

.EXAMPLE
    powershell -ExecutionPolicy Bypass -File scripts\import-db.ps1 -File D:\backup\produksi.sql -Migrate
#>
param(
    [Parameter(Mandatory = $true)] [string] $File,
    [switch] $Migrate,
    [switch] $Force
)

$ErrorActionPreference = 'Stop'
Set-Location (Split-Path -Parent $PSScriptRoot)

if (-not (Test-Path $File)) { Write-Host "File tidak ditemukan: $File" -ForegroundColor Red; exit 1 }
$File = (Resolve-Path $File).Path
$compose = @('compose', '--env-file', '.env.docker')

if (-not $Force) {
    $answer = Read-Host 'Database Docker lokal akan DIGANTI dengan isi dump ini. Lanjut? (y/N)'
    if ($answer -ne 'y') { Write-Host 'Dibatalkan.'; exit 1 }
}

function Run([string[]] $arguments, [string] $label) {
    & docker @arguments
    if ($LASTEXITCODE -ne 0) { Write-Host "Gagal: $label" -ForegroundColor Red; exit 1 }
}

# Tanpa tanda kutip ganda di dalam perintah: PowerShell 5.1 membuangnya saat memanggil program lain.
Write-Host '==> Mengosongkan database' -ForegroundColor Cyan
Run ($compose + @('exec', '-T', 'db', 'sh', '-c', 'export MYSQL_PWD=$MARIADB_ROOT_PASSWORD; mariadb-admin -uroot -f drop $MARIADB_DATABASE && mariadb-admin -uroot create $MARIADB_DATABASE')) 'drop/create database'

Write-Host '==> Mengimpor dump' -ForegroundColor Cyan
Run ($compose + @('cp', $File, 'db:/tmp/import.sql')) 'menyalin dump'
Run ($compose + @('exec', '-T', 'db', 'sh', '-c', 'export MYSQL_PWD=$MARIADB_PASSWORD; mariadb -u$MARIADB_USER $MARIADB_DATABASE < /tmp/import.sql; status=$?; rm -f /tmp/import.sql; exit $status')) 'impor dump'

if ($Migrate) {
    Write-Host '==> Menjalankan migration (seperti saat deploy)' -ForegroundColor Cyan
    Run ($compose + @('exec', '-T', 'app', 'php', 'artisan', 'migrate', '--force')) 'migrate'
    Run ($compose + @('exec', '-T', 'app', 'php', 'artisan', 'optimize:clear')) 'optimize:clear'
}

Write-Host 'Selesai. Buka http://localhost:8082' -ForegroundColor Green

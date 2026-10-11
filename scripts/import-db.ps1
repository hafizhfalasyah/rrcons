<#
.SYNOPSIS
    Mengganti isi database LOKAL dengan file dump SQL (mis. export phpMyAdmin produksi) untuk gladi resik.

.DESCRIPTION
    Default : database MariaDB di Docker (service "db").
    -Native : MySQL/MariaDB lokal tanpa Docker (mis. XAMPP), memakai DB_HOST/DB_PORT/DB_DATABASE/
              DB_USERNAME/DB_PASSWORD dari file .env proyek.
    Database lokal dikosongkan (DROP + CREATE), dump diimpor, lalu (opsional) migration dijalankan
    seperti saat deploy. Tidak pernah menyentuh database hosting.

.EXAMPLE
    powershell -ExecutionPolicy Bypass -File scripts\import-db.ps1 -File D:\backup\produksi.sql -Migrate
    powershell -ExecutionPolicy Bypass -File scripts\import-db.ps1 -File D:\backup\produksi.sql -Migrate -Native
#>
param(
    [Parameter(Mandatory = $true)] [string] $File,
    [switch] $Migrate,
    [switch] $Force,
    [switch] $Native,
    [string] $MysqlPath = 'C:\xampp\mysql\bin\mysql.exe',
    [string] $EnvFile = '.env'
)

$ErrorActionPreference = 'Stop'
Set-Location (Split-Path -Parent $PSScriptRoot)

function Fail([string] $message) { Write-Host "Gagal: $message" -ForegroundColor Red; exit 1 }
function Step([string] $message) { Write-Host "==> $message" -ForegroundColor Cyan }

if (-not (Test-Path $File)) { Fail "File tidak ditemukan: $File" }
$File = (Resolve-Path $File).Path

if ($Native) {
    if (-not (Test-Path $MysqlPath)) { Fail "mysql.exe tidak ditemukan di $MysqlPath (pakai -MysqlPath)." }
    if (-not (Test-Path $EnvFile)) { Fail "File $EnvFile tidak ditemukan." }

    $envValues = @{}
    foreach ($line in Get-Content $EnvFile) {
        if ($line -match '^\s*(DB_[A-Z_]+)\s*=\s*(.*)\s*$') { $envValues[$Matches[1]] = $Matches[2].Trim('"', "'") }
    }
    $dbHost = if ($envValues['DB_HOST']) { $envValues['DB_HOST'] } else { '127.0.0.1' }
    $dbPort = if ($envValues['DB_PORT']) { $envValues['DB_PORT'] } else { '3306' }
    $database = $envValues['DB_DATABASE']
    $user = $envValues['DB_USERNAME']
    if (-not $database -or -not $user) { Fail 'DB_DATABASE dan DB_USERNAME di .env harus diisi.' }
    $target = "$database di $dbHost`:$dbPort (lokal, $EnvFile)"
} else {
    $target = 'database Docker (service db)'
}

if (-not $Force) {
    $answer = Read-Host "Database $target akan DIGANTI dengan isi dump ini. Lanjut? (y/N)"
    if ($answer -ne 'y') { Write-Host 'Dibatalkan.'; exit 1 }
}

if ($Native) {
    $env:MYSQL_PWD = $envValues['DB_PASSWORD']
    $connection = @('-h', $dbHost, '-P', $dbPort, '-u', $user)

    Step 'Mengosongkan database'
    $sql = 'DROP DATABASE IF EXISTS `' + $database + '`; CREATE DATABASE `' + $database + '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;'
    & $MysqlPath @connection -e $sql
    if ($LASTEXITCODE -ne 0) { Fail 'drop/create database (user di .env harus punya hak CREATE/DROP).' }

    Step 'Mengimpor dump'
    # Dump dari mariadb-dump versi baru diawali baris "sandbox mode" yang tidak dikenal klien MySQL lama (mis. XAMPP)
    $source = $File
    $reader = New-Object System.IO.StreamReader($File)
    $firstLine = $reader.ReadLine()
    if ($firstLine -match 'enable the sandbox mode') {
        $source = Join-Path $env:TEMP ('rrcons-import-' + [guid]::NewGuid().ToString('N') + '.sql')
        $writer = New-Object System.IO.StreamWriter($source, $false, (New-Object System.Text.UTF8Encoding $false))
        while ($null -ne ($line = $reader.ReadLine())) { $writer.WriteLine($line) }
        $writer.Close()
    }
    $reader.Close()

    $process = Start-Process -FilePath $MysqlPath -ArgumentList ($connection + @('--default-character-set=utf8mb4', $database)) -RedirectStandardInput $source -NoNewWindow -Wait -PassThru
    if ($source -ne $File) { Remove-Item $source -Force }
    if ($process.ExitCode -ne 0) { Fail 'impor dump.' }
    Remove-Item Env:MYSQL_PWD -ErrorAction SilentlyContinue

    if ($Migrate) {
        Step 'Menjalankan migration (seperti saat deploy)'
        # pastikan artisan memakai database yang sama dengan yang baru diimpor
        $env:DB_CONNECTION = 'mysql'; $env:DB_HOST = $dbHost; $env:DB_PORT = $dbPort
        $env:DB_DATABASE = $database; $env:DB_USERNAME = $user; $env:DB_PASSWORD = $envValues['DB_PASSWORD']
        & php artisan migrate --force
        if ($LASTEXITCODE -ne 0) { Fail 'migrate' }
        & php artisan optimize:clear
    }

    Write-Host 'Selesai. Jalankan: php artisan serve  lalu buka http://localhost:8000' -ForegroundColor Green
    exit 0
}

function Run([string[]] $arguments, [string] $label) {
    & docker @arguments
    if ($LASTEXITCODE -ne 0) { Fail $label }
}

$compose = @('compose', '--env-file', '.env.docker')

# Tanpa tanda kutip ganda di dalam perintah: PowerShell 5.1 membuangnya saat memanggil program lain.
Step 'Mengosongkan database'
Run ($compose + @('exec', '-T', 'db', 'sh', '-c', 'export MYSQL_PWD=$MARIADB_ROOT_PASSWORD; mariadb-admin -uroot -f drop $MARIADB_DATABASE && mariadb-admin -uroot create $MARIADB_DATABASE')) 'drop/create database'

Step 'Mengimpor dump'
Run ($compose + @('cp', $File, 'db:/tmp/import.sql')) 'menyalin dump'
Run ($compose + @('exec', '-T', 'db', 'sh', '-c', 'export MYSQL_PWD=$MARIADB_PASSWORD; mariadb -u$MARIADB_USER $MARIADB_DATABASE < /tmp/import.sql; status=$?; rm -f /tmp/import.sql; exit $status')) 'impor dump'

if ($Migrate) {
    Step 'Menjalankan migration (seperti saat deploy)'
    Run ($compose + @('exec', '-T', 'app', 'php', 'artisan', 'migrate', '--force')) 'migrate'
    Run ($compose + @('exec', '-T', 'app', 'php', 'artisan', 'optimize:clear')) 'optimize:clear'
}

Write-Host 'Selesai. Buka http://localhost:8082' -ForegroundColor Green

<#
.SYNOPSIS
    Membuat paket ZIP siap upload ke shared hosting (tanpa Composer/Node di server).

.DESCRIPTION
    1. Memastikan PHP lokal >= 8.5 dan working tree bersih (paket = commit yang sedang aktif).
    2. Menjalankan test (bisa dilewati dengan -SkipTests).
    3. Mengekspor commit aktif (git archive), memasang dependensi produksi
       (composer install --no-dev --optimize-autoloader) dan membuang file khusus development.
    4. Menghasilkan release\rrcons-<commit>-<tanggal>.zip

    Isi ZIP TIDAK mengandung .env, storage/app/public (file upload) maupun node_modules.

.EXAMPLE
    powershell -ExecutionPolicy Bypass -File scripts\build-release.ps1
#>
param(
    [switch] $SkipTests
)

$ErrorActionPreference = 'Stop'
$root = Split-Path -Parent $PSScriptRoot
Set-Location $root

function Step([string] $message) { Write-Host "==> $message" -ForegroundColor Cyan }
function Fail([string] $message) { Write-Host "ERROR: $message" -ForegroundColor Red; exit 1 }

Step 'Memeriksa PHP & git'
& php -r "exit(version_compare(PHP_VERSION, '8.5.0', '>=') ? 0 : 1);"
if ($LASTEXITCODE -ne 0) { Fail 'PHP lokal harus 8.5 atau lebih baru.' }

$dirty = & git status --porcelain
if ($dirty) { Fail 'Masih ada perubahan yang belum di-commit. Commit/stash dulu agar paket sama persis dengan commit.' }

$sha = (& git rev-parse --short HEAD).Trim()
$branch = (& git rev-parse --abbrev-ref HEAD).Trim()
$stamp = Get-Date -Format 'yyyyMMdd-HHmm'

if (-not $SkipTests) {
    Step 'Menjalankan test (php artisan test)'
    & php artisan test
    if ($LASTEXITCODE -ne 0) { Fail 'Test gagal, paket tidak dibuat.' }
}

$stage = Join-Path $env:TEMP "rrcons-release-$sha"
if (Test-Path $stage) { Remove-Item $stage -Recurse -Force }
New-Item -ItemType Directory -Force $stage | Out-Null

Step "Mengekspor commit $sha ($branch)"
$tarball = Join-Path $env:TEMP "rrcons-release-$sha.tar"
& git archive --format=tar --output="$tarball" HEAD
if ($LASTEXITCODE -ne 0) { Fail 'git archive gagal.' }
& "$env:SystemRoot\System32\tar.exe" -xf "$tarball" -C "$stage"
if ($LASTEXITCODE -ne 0) { Fail 'Ekstrak arsip gagal.' }
Remove-Item $tarball -Force

Step 'Memasang dependensi produksi (composer install --no-dev)'
& composer install --working-dir="$stage" --no-dev --optimize-autoloader --no-interaction --no-progress
if ($LASTEXITCODE -ne 0) { Fail 'composer install gagal.' }

Step 'Membuang file khusus development'
$devOnly = @(
    'tests', 'docker', '.claude', '.ai', 'scripts',
    'Dockerfile', 'compose.yaml', '.dockerignore', '.env.docker.example', 'README-Docker.md',
    'phpunit.xml', '.editorconfig', '.gitattributes', '.gitignore',
    'package.json', 'package-lock.json', 'vite.config.js', 'tailwind.config.js',
    'AGENTS.md', 'CLAUDE.md'
)
foreach ($item in $devOnly) {
    $path = Join-Path $stage $item
    if (Test-Path $path) { Remove-Item $path -Recurse -Force }
}

Step 'Membuat ZIP'
$outDir = Join-Path $root 'release'
New-Item -ItemType Directory -Force $outDir | Out-Null
$zip = Join-Path $outDir "rrcons-$sha-$stamp.zip"
if (Test-Path $zip) { Remove-Item $zip -Force }
# tar.exe bawaan Windows menulis ZIP dengan pemisah "/" (aman diekstrak di Linux/cPanel)
& "$env:SystemRoot\System32\tar.exe" -a -c -f "$zip" -C "$stage" .
if ($LASTEXITCODE -ne 0) { Fail 'Pembuatan ZIP gagal.' }
Remove-Item $stage -Recurse -Force

$size = [math]::Round((Get-Item $zip).Length / 1MB, 1)
Write-Host ''
Write-Host "Selesai: $zip ($size MB)" -ForegroundColor Green
Write-Host 'Lanjutkan dengan langkah di docs\DEPLOY-SHARED-HOSTING.md'

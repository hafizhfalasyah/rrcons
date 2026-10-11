<#
.SYNOPSIS
    Membuat paket ZIP siap upload ke shared hosting (server tidak perlu Composer, Node.js, maupun Terminal).

.DESCRIPTION
    1. Memastikan working tree bersih (paket = commit yang sedang aktif) dan PHP 8.5 tersedia.
    2. Menjalankan test (bisa dilewati dengan -SkipTests).
    3. Mengekspor commit aktif (git archive), memasang dependensi produksi
       (composer install --no-dev --optimize-autoloader) dan membuang file khusus development.
    4. Menghasilkan release\rrcons-<commit>-<tanggal>.zip

    Isi ZIP TIDAK mengandung .env, storage/app/public (file upload) maupun node_modules.
    CSS publik sudah ter-compile di repo (public/front/css/app.css), jadi Node.js tidak dibutuhkan.

.PARAMETER UseDocker
    Jalankan test & composer di container Docker (bila PHP 8.5/Composer tidak terpasang di Windows).
    Stack harus sudah berjalan: docker compose --env-file .env.docker up -d

.PARAMETER WithWebInstaller
    Sertakan web installer sekali pakai (public/install-<acak>.php) untuk hosting tanpa Terminal & Cron Job.
    URL dan kunci rahasianya ditampilkan di akhir dan disimpan di release\*-installer.txt.

.EXAMPLE
    powershell -ExecutionPolicy Bypass -File scripts\build-release.ps1
    powershell -ExecutionPolicy Bypass -File scripts\build-release.ps1 -UseDocker -WithWebInstaller
#>
param(
    [switch] $SkipTests,
    [switch] $UseDocker,
    [switch] $WithWebInstaller
)

$ErrorActionPreference = 'Stop'
$root = Split-Path -Parent $PSScriptRoot
Set-Location $root

function Step([string] $message) { Write-Host "==> $message" -ForegroundColor Cyan }
function Fail([string] $message) { Write-Host "ERROR: $message" -ForegroundColor Red; exit 1 }
function RandomHex([int] $bytes) {
    $buffer = New-Object byte[] $bytes
    [System.Security.Cryptography.RandomNumberGenerator]::Create().GetBytes($buffer)
    return -join ($buffer | ForEach-Object { $_.ToString('x2') })
}

Step 'Memeriksa git & PHP'
$dirty = & git status --porcelain
if ($dirty) { Fail 'Masih ada perubahan yang belum di-commit. Commit/stash dulu agar paket sama persis dengan commit.' }

$compose = @('compose', '--env-file', '.env.docker')
if ($UseDocker) {
    & docker image inspect rrcons-app *> $null
    if ($LASTEXITCODE -ne 0) { Fail 'Image rrcons-app belum ada. Jalankan: docker compose --env-file .env.docker up -d --build' }
} else {
    & php -r "exit(version_compare(PHP_VERSION, '8.5.0', '>=') ? 0 : 1);"
    if ($LASTEXITCODE -ne 0) { Fail 'PHP lokal harus 8.5 atau lebih baru (atau pakai -UseDocker).' }
}

$sha = (& git rev-parse --short HEAD).Trim()
$branch = (& git rev-parse --abbrev-ref HEAD).Trim()
$stamp = Get-Date -Format 'yyyyMMdd-HHmm'

if (-not $SkipTests) {
    Step 'Menjalankan test (php artisan test)'
    if ($UseDocker) { & docker @compose exec -T app php artisan test } else { & php artisan test }
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
if ($UseDocker) {
    & docker run --rm -v "${stage}:/app" -w /app --entrypoint composer rrcons-app install --no-dev --optimize-autoloader --no-interaction --no-progress
} else {
    & composer install --working-dir="$stage" --no-dev --optimize-autoloader --no-interaction --no-progress
}
if ($LASTEXITCODE -ne 0) { Fail 'composer install gagal.' }

$installer = $null
if ($WithWebInstaller) {
    Step 'Menyertakan web installer sekali pakai'
    $key = RandomHex 24
    $installer = 'install-' + (RandomHex 8) + '.php'
    $sha256 = [System.Security.Cryptography.SHA256]::Create()
    $hash = -join ($sha256.ComputeHash([System.Text.Encoding]::UTF8.GetBytes($key)) | ForEach-Object { $_.ToString('x2') })
    $content = [System.IO.File]::ReadAllText((Join-Path $stage 'scripts\stubs\web-installer.php'))
    $content = $content.Replace("const INSTALLER_KEY_HASH = '__KEY_HASH__';", "const INSTALLER_KEY_HASH = '$hash';")
    [System.IO.File]::WriteAllText((Join-Path $stage "public\$installer"), $content, (New-Object System.Text.UTF8Encoding $false))
}

Step 'Membuang file khusus development'
$devOnly = @(
    'tests', 'docker', '.claude', '.ai', 'scripts', 'docs',
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

if ($installer) {
    $info = Join-Path $outDir "rrcons-$sha-$stamp-installer.txt"
    @(
        "Web installer RRCONS (sekali pakai, terhapus otomatis setelah berhasil)",
        "Buka setelah upload, ekstrak dan .env siap:",
        "  https://DOMAIN-ANDA/$installer`?key=$key",
        "Jangan bagikan kunci ini. File ini (release\*-installer.txt) tidak ikut di-commit."
    ) | Set-Content -Path $info -Encoding UTF8
    Write-Host ''
    Write-Host "Web installer : https://DOMAIN-ANDA/$installer`?key=$key" -ForegroundColor Yellow
    Write-Host "Disimpan di   : $info"
}

Write-Host 'Lanjutkan dengan langkah di docs\DEPLOY-SHARED-HOSTING.md'

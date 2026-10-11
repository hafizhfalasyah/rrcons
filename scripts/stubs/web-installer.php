<?php

/**
 * RRCONS — web installer sekali pakai untuk shared hosting TANPA Terminal & Cron Job.
 *
 * Dibuat oleh: scripts/build-release.ps1 -WithWebInstaller (nama file & kunci acak per build).
 * Menjalankan: link public/storage, php artisan migrate --force, dan cache Laravel.
 * File ini MENGHAPUS DIRINYA SENDIRI setelah langkah selesai. Bila gagal terhapus, hapus manual.
 */

declare(strict_types=1);

// sha256 dari kunci rahasia (kunci aslinya hanya ditampilkan sekali saat build)
const INSTALLER_KEY_HASH = '__KEY_HASH__';

header('X-Robots-Tag: noindex, nofollow');
header('Cache-Control: no-store');

$key = (string) ($_POST['key'] ?? $_GET['key'] ?? '');

if (INSTALLER_KEY_HASH === '__KEY' . '_HASH__' || !hash_equals(INSTALLER_KEY_HASH, hash('sha256', $key))) {
    http_response_code(404);
    exit;
}

header('Content-Type: text/html; charset=UTF-8');

/**
 * Folder aplikasi Laravel: induk folder web (struktur standar) atau path yang dipakai index.php
 * (bila isi public/ disalin ke public_html dan index.php disesuaikan).
 */
function rrcons_base_path(): ?string
{
    $candidates = [dirname(__DIR__)];
    $index = @file_get_contents(__DIR__ . '/index.php');

    if ($index && preg_match("#__DIR__\s*\.\s*'(/[^']*)/vendor/autoload\.php'#", $index, $match)) {
        $candidates[] = __DIR__ . $match[1];
    }

    foreach ($candidates as $candidate) {
        if (is_file($candidate . '/artisan') && is_file($candidate . '/vendor/autoload.php')) {
            return realpath($candidate) ?: $candidate;
        }
    }

    return null;
}

function rrcons_e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

$base = rrcons_base_path();
$checks = [];
$checks[] = ['PHP ' . PHP_VERSION . ' (butuh 8.5+)', version_compare(PHP_VERSION, '8.5.0', '>=')];

foreach (['bcmath', 'ctype', 'curl', 'dom', 'fileinfo', 'gd', 'intl', 'mbstring', 'openssl', 'pdo_mysql', 'tokenizer', 'xml', 'zip'] as $extension) {
    $checks[] = ["Ekstensi {$extension}", extension_loaded($extension)];
}

$checks[] = ['Folder aplikasi ditemukan' . ($base ? " ({$base})" : ''), $base !== null];

if ($base) {
    $checks[] = ['File .env ada', is_file($base . '/.env')];
    $checks[] = ['storage/ bisa ditulis', is_writable($base . '/storage')];
    $checks[] = ['bootstrap/cache/ bisa ditulis', is_writable($base . '/bootstrap/cache')];
    $checks[] = ['Fungsi symlink() tersedia', function_exists('symlink')];
}

$ready = !in_array(false, array_column($checks, 1), true);
$run = $ready && ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
$log = [];
$failed = false;
$pending = null;
$dbError = null;

if ($ready) {
    try {
        require $base . '/vendor/autoload.php';
        $app = require $base . '/bootstrap/app.php';
        $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
        $kernel->bootstrap();

        $app->make('db')->connection()->getPdo();

        $migrator = $app->make('migrator');
        $files = $migrator->getMigrationFiles([$app->databasePath('migrations')]);
        $ran = $migrator->getRepository()->repositoryExists() ? $migrator->getRepository()->getRan() : [];
        $pending = array_values(array_diff(array_keys($files), $ran));
    } catch (Throwable $exception) {
        $dbError = $exception->getMessage();
        $ready = false;
        $run = false;
    }
}

if ($run) {
    @set_time_limit(0);
    ignore_user_abort(true);

    $artisan = function (string $command, array $parameters = []) use ($kernel, &$log, &$failed): void {
        if ($failed) {
            return;
        }

        try {
            $status = $kernel->call($command, $parameters);
            $log[] = ['php artisan ' . $command, trim($kernel->output()), $status === 0];
            $failed = $status !== 0;
        } catch (Throwable $exception) {
            $log[] = ['php artisan ' . $command, $exception->getMessage(), false];
            $failed = true;
        }
    };

    $artisan('optimize:clear');

    // link public/storage -> storage/app/public di folder web yang sedang dipakai
    $link = __DIR__ . '/storage';
    $target = $base . '/storage/app/public';

    if (is_link($link) || is_dir($link)) {
        $log[] = ['link public/storage', 'Sudah ada, tidak diubah.', true];
    } elseif (@symlink($target, $link)) {
        $log[] = ['link public/storage', "{$link} -> {$target}", true];
    } else {
        $log[] = ['link public/storage', 'Gagal membuat symlink. Minta hosting mengaktifkan symlink() atau buat link lewat Cron Job (lihat docs).', false];
        $failed = true;
    }

    $artisan('migrate', ['--force' => true]);
    $artisan('config:cache');
    $artisan('route:cache');
    $artisan('view:cache');

    $deleted = !$failed && @unlink(__FILE__);
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>RRCONS — Web Installer</title>
    <style>
        body { font-family: system-ui, sans-serif; max-width: 860px; margin: 2rem auto; padding: 0 1rem; color: #1f2937; }
        .ok { color: #047857; } .fail { color: #b91c1c; }
        pre { background: #f3f4f6; padding: .75rem; overflow-x: auto; white-space: pre-wrap; }
        button { background: #2563eb; color: #fff; border: 0; padding: .7rem 1.4rem; border-radius: .4rem; font-size: 1rem; cursor: pointer; }
        .box { border: 1px solid #e5e7eb; border-radius: .5rem; padding: 1rem; margin: 1rem 0; }
    </style>
</head>
<body>
    <h1>RRCONS — Web Installer</h1>

    <?php if (!$run): ?>
        <div class="box">
            <h2>Pemeriksaan server</h2>
            <ul>
                <?php foreach ($checks as [$label, $passed]): ?>
                    <li class="<?= $passed ? 'ok' : 'fail' ?>"><?= $passed ? '✔' : '✘' ?> <?= rrcons_e($label) ?></li>
                <?php endforeach; ?>
                <?php if ($dbError !== null): ?>
                    <li class="fail">✘ Koneksi database: <?= rrcons_e($dbError) ?></li>
                <?php elseif ($pending !== null): ?>
                    <li class="ok">✔ Koneksi database OK — <?= count($pending) ?> migration akan dijalankan</li>
                <?php endif; ?>
            </ul>
            <?php if (!empty($pending)): ?>
                <pre><?= rrcons_e(implode("\n", $pending)) ?></pre>
            <?php endif; ?>
        </div>

        <?php if ($ready): ?>
            <form method="post">
                <input type="hidden" name="key" value="<?= rrcons_e($key) ?>">
                <p>Pastikan database produksi <strong>sudah dibackup</strong>. Proses bisa memakan waktu 1–2 menit, jangan tutup halaman.</p>
                <button type="submit">Jalankan deploy</button>
            </form>
        <?php else: ?>
            <p class="fail">Perbaiki item bertanda ✘ terlebih dahulu, lalu muat ulang halaman ini.</p>
        <?php endif; ?>
    <?php else: ?>
        <?php foreach ($log as [$step, $output, $passed]): ?>
            <div class="box">
                <strong class="<?= $passed ? 'ok' : 'fail' ?>"><?= $passed ? '✔' : '✘' ?> <?= rrcons_e($step) ?></strong>
                <?php if ($output !== ''): ?><pre><?= rrcons_e($output) ?></pre><?php endif; ?>
            </div>
        <?php endforeach; ?>

        <?php if ($failed): ?>
            <p class="fail"><strong>Deploy belum selesai.</strong> Perbaiki masalah di atas lalu muat ulang halaman ini. File installer belum dihapus.</p>
        <?php elseif (!empty($deleted)): ?>
            <p class="ok"><strong>Deploy selesai.</strong> File installer ini sudah terhapus otomatis. Buka website untuk pemeriksaan akhir.</p>
        <?php else: ?>
            <p class="fail"><strong>Deploy selesai, tetapi file installer gagal terhapus.</strong> HAPUS file <?= rrcons_e(basename(__FILE__)) ?> sekarang lewat File Manager.</p>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>

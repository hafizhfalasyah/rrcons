<?php

use App\Support\LegacyTimezoneFix;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Koreksi timestamp yang tersimpan saat aplikasi masih memakai timezone UTC
     * (tampil 7 jam lebih awal setelah timezone diganti ke Asia/Jakarta).
     *
     * Hanya dijalankan otomatis bila pergantian timezone terjadi pada `php artisan migrate`
     * yang sama (migrasi 2026_10_11 baru dijalankan). Bila versi Asia/Jakarta sudah berjalan
     * sebelumnya, waktu pergantian tidak bisa diketahui pasti, jadi gunakan perintah:
     *   php artisan rrcons:fix-legacy-timezone --switched-at="YYYY-MM-DD HH:MM" --dry-run
     *
     * Semua nilai lama dibackup ke tabel legacy_timezone_backups; rollback memulihkannya.
     */
    public function up(): void
    {
        if (!app()->bound('rrcons.timezone_switched_at')) {
            $this->say('Timezone UTC -> ' . config('app.timezone') . ' was switched in an earlier deployment: automatic correction skipped. '
                . 'Run "php artisan rrcons:fix-legacy-timezone --switched-at=\"YYYY-MM-DD HH:MM\" --dry-run" with the time that deployment went live.');

            return;
        }

        $report = (new LegacyTimezoneFix(app('rrcons.timezone_switched_at')))->run();

        $this->say('Legacy UTC timestamps corrected: ' . json_encode(array_filter($report)));
        Log::info('Legacy UTC timestamps corrected', $report);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $report = LegacyTimezoneFix::restore();
        Log::info('Legacy UTC timestamps restored', $report);

        Schema::dropIfExists(LegacyTimezoneFix::BACKUP_TABLE);
    }

    private function say(string $message): void
    {
        if (app()->runningInConsole() && defined('STDOUT') && !app()->runningUnitTests()) {
            fwrite(STDOUT, '  ' . $message . PHP_EOL);
        }
    }
};

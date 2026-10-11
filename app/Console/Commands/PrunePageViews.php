<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Hapus data mentah page view yang sudah sangat lama (opsional, tidak dijadwalkan otomatis).
 * Tabel visitors (pencatat lama) tidak disentuh.
 */
class PrunePageViews extends Command
{
    protected $signature = 'rrcons:prune-page-views
        {--days= : Simpan data N hari terakhir (default: config analytics.retention_days)}
        {--dry-run : Hanya tampilkan jumlah data yang akan dihapus}
        {--force : Jalankan tanpa konfirmasi}';

    protected $description = 'Hapus data page view yang lebih tua dari masa simpan (retensi)';

    public function handle(): int
    {
        $days = (int) ($this->option('days') ?: config('analytics.retention_days'));

        if ($days < 30) {
            $this->error('Retention must be at least 30 days.');

            return self::INVALID;
        }

        $cutoff = now()->subDays($days)->startOfDay();
        $count = DB::table('page_views')->where('created_at', '<', $cutoff)->count();

        $this->info("{$count} page views older than {$cutoff->toDateString()} ({$days} days).");

        if ($this->option('dry-run') || $count === 0) {
            return self::SUCCESS;
        }

        if (!$this->option('force') && !$this->confirm('Delete them permanently?')) {
            return self::FAILURE;
        }

        do {
            $deleted = DB::table('page_views')->where('created_at', '<', $cutoff)->limit(5000)->delete();
        } while ($deleted > 0);

        $this->info('Done.');

        return self::SUCCESS;
    }
}

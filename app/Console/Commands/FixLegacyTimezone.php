<?php

namespace App\Console\Commands;

use App\Support\LegacyTimezoneFix;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class FixLegacyTimezone extends Command
{
    protected $signature = 'rrcons:fix-legacy-timezone
        {--switched-at= : Waktu (zona aplikasi, mis. WIB) ketika versi ber-timezone Asia/Jakarta mulai berjalan, format "YYYY-MM-DD HH:MM"}
        {--dry-run : Hanya tampilkan jumlah data yang akan dikoreksi, tanpa mengubah apa pun}
        {--restore : Kembalikan semua nilai dari tabel backup}
        {--force : Jalankan tanpa konfirmasi}';

    protected $description = 'Koreksi timestamp lama yang tersimpan dalam UTC (tampil 7 jam lebih awal) dengan backup & restore yang aman';

    public function handle(): int
    {
        if ($this->option('restore')) {
            if (!$this->option('force') && !$this->confirm('Restore all timestamps from ' . LegacyTimezoneFix::BACKUP_TABLE . '?')) {
                return self::FAILURE;
            }

            $this->table(['Column', 'Restored rows'], $this->rows(LegacyTimezoneFix::restore()));

            return self::SUCCESS;
        }

        if (LegacyTimezoneFix::offsetMinutes() === 0) {
            $this->warn('The application timezone is UTC, there is nothing to correct.');

            return self::SUCCESS;
        }

        $switchedAt = $this->option('switched-at');

        if (!$switchedAt) {
            $this->error('Provide --switched-at="YYYY-MM-DD HH:MM": the time (' . config('app.timezone') . ') when the version using '
                . config('app.timezone') . ' went live. Data written before that moment was stored in UTC.');

            return self::INVALID;
        }

        try {
            $switchedAt = Carbon::parse($switchedAt, config('app.timezone'));
        } catch (\Throwable $e) {
            $this->error('Invalid --switched-at value.');

            return self::INVALID;
        }

        $dryRun = (bool) $this->option('dry-run');
        $fix = new LegacyTimezoneFix($switchedAt, $dryRun);

        $this->info(sprintf(
            'Values up to %s were written in UTC and will be shifted by %+d minutes.',
            $fix->threshold(),
            LegacyTimezoneFix::offsetMinutes()
        ));

        if (!$dryRun && !$this->option('force') && !$this->confirm('A backup of every changed value is stored in ' . LegacyTimezoneFix::BACKUP_TABLE . '. Continue?')) {
            return self::FAILURE;
        }

        $this->table(['Column', $dryRun ? 'Rows to correct' : 'Corrected rows'], $this->rows($fix->run()));

        if ($dryRun) {
            $this->comment('Dry run: nothing was changed.');
        }

        return self::SUCCESS;
    }

    private function rows(array $report): array
    {
        return collect($report)->map(fn ($count, $column) => [$column, $count])->values()->all();
    }
}

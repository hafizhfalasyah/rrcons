<?php

namespace App\Console\Commands;

use App\Support\ImageOptimizer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

/**
 * Optimalkan gambar yang SUDAH diunggah (gambar baru otomatis dioptimalkan saat upload).
 * Opsional & manual: file asli selalu dibackup lebih dulu dan bisa dipulihkan.
 */
class OptimizeImages extends Command
{
    protected $signature = 'rrcons:optimize-images
        {--dry-run : Hanya tampilkan perkiraan penghematan, tanpa mengubah file}
        {--restore= : Pulihkan file asli dari backup (nama folder backup, mis. 20261011-101500)}
        {--force : Jalankan tanpa konfirmasi}';

    protected $description = 'Perkecil ukuran gambar unggahan lama tanpa perubahan visual (dengan backup)';

    public function handle(): int
    {
        if ($restore = $this->option('restore')) {
            return $this->restore($restore);
        }

        $disk = Storage::disk('public');
        $dryRun = (bool) $this->option('dry-run');
        $files = collect($disk->allFiles('back'))
            ->filter(fn ($file) => preg_match('/\.(jpe?g|png|webp)$/i', $file))
            ->values();

        if ($files->isEmpty()) {
            $this->info('No images found.');

            return self::SUCCESS;
        }

        if (!$dryRun && !$this->option('force')
            && !$this->confirm("Optimize {$files->count()} images? Originals are backed up first.")) {
            return self::FAILURE;
        }

        $backupDir = 'image-backups/' . now()->format('Ymd-His');
        $rows = [];
        $before = $after = 0;

        foreach ($files as $file) {
            $absolute = $disk->path($file);

            // simulasi dulu supaya hanya file yang benar-benar mengecil yang dibackup & diganti
            $result = ImageOptimizer::optimize($absolute, false);

            if (!$result || $result['after'] >= $result['before']) {
                continue;
            }

            if (!$dryRun) {
                Storage::disk('local')->put($backupDir . '/' . $file, file_get_contents($absolute));
                $result = ImageOptimizer::optimize($absolute) ?? $result;
            }

            $before += $result['before'];
            $after += $result['after'];
            $rows[] = [$file, $this->kb($result['before']), $this->kb($result['after']), $result['resized'] ? 'yes' : ''];
        }

        $this->table(['File', 'Before', 'After', 'Resized'], $rows);
        $this->info(sprintf('%d of %d images %s: %s -> %s (%s saved).',
            count($rows), $files->count(), $dryRun ? 'can be optimized' : 'optimized',
            $this->kb($before), $this->kb($after), $this->kb($before - $after)));

        if ($dryRun) {
            $this->comment('Dry run: nothing was changed.');
        } elseif ($rows) {
            $this->comment('Originals backed up in storage/app/' . $backupDir
                . '. Restore with: php artisan rrcons:optimize-images --restore=' . basename($backupDir));
        }

        return self::SUCCESS;
    }

    private function restore(string $name): int
    {
        $name = basename($name);
        $source = 'image-backups/' . $name;

        if (!Storage::disk('local')->exists($source)) {
            $this->error("Backup {$name} not found in storage/app/image-backups.");

            return self::INVALID;
        }

        $count = 0;

        foreach (Storage::disk('local')->allFiles($source) as $backup) {
            $relative = substr($backup, strlen($source) + 1);
            File::ensureDirectoryExists(dirname(Storage::disk('public')->path($relative)));
            Storage::disk('public')->put($relative, Storage::disk('local')->get($backup));
            $count++;
        }

        $this->info("{$count} images restored from {$name}.");

        return self::SUCCESS;
    }

    private function kb(int $bytes): string
    {
        return number_format($bytes / 1024, 0) . ' KB';
    }
}

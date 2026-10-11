<?php

namespace App\Http\Controllers\Concerns;

use App\Support\ImageOptimizer;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Upload & hapus file di disk "public" (storage/app/public).
 *
 * Path yang disimpan ke database selalu relatif terhadap disk public,
 * misalnya "back/home/hero/64f1c2.jpg", sehingga view cukup memakai
 * asset('storage/' . $path).
 */
trait ManagesPublicFiles
{
    protected function storePublicFile(UploadedFile $file, string $directory): string
    {
        $filename = uniqid() . '.' . $file->getClientOriginalExtension();

        $path = $file->storeAs($directory, $filename, 'public');

        // perkecil ukuran file gambar (tanpa perubahan visual) agar halaman lebih cepat dimuat
        if ($path && ImageOptimizer::enabled()) {
            ImageOptimizer::optimize(Storage::disk('public')->path($path));
        }

        return $path;
    }

    /**
     * Data lama hanya menyimpan nama file, jadi direktori dipakai sebagai fallback.
     */
    protected function deletePublicFile(?string $path, string $legacyDirectory): void
    {
        if (!$path) {
            return;
        }

        $disk = Storage::disk('public');
        $candidate = str_contains($path, '/') ? $path : trim($legacyDirectory, '/') . '/' . $path;

        if ($disk->exists($candidate)) {
            $disk->delete($candidate);
        }
    }

    protected function replacePublicFile(?string $oldPath, UploadedFile $file, string $directory): string
    {
        $this->deletePublicFile($oldPath, $directory);

        return $this->storePublicFile($file, $directory);
    }
}

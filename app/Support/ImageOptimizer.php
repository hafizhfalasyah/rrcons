<?php

namespace App\Support;

use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

/**
 * Memperkecil ukuran file gambar tanpa perubahan visual yang terlihat:
 * - JPEG/WebP di-encode ulang dengan kualitas tinggi (85, progresif)
 * - PNG di-encode ulang tanpa kehilangan kualitas (lossless)
 * - Resolusi hanya diturunkan bila sisi terpanjang melebihi MAX_DIMENSION
 *   (masih tajam untuk layar retina pada lebar tampilan website ini)
 * Hasil hanya dipakai bila benar-benar lebih kecil; kegagalan tidak pernah
 * menggagalkan upload (file asli tetap dipakai).
 */
class ImageOptimizer
{
    public const MAX_DIMENSION = 2560;
    public const QUALITY = 85;

    /** Hanya disimpan bila minimal sekian persen lebih kecil */
    private const MIN_SAVING = 0.05;

    private const EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];

    public static function enabled(): bool
    {
        return (bool) config('filesystems.optimize_uploaded_images', true);
    }

    /**
     * @param  bool  $write  false = hanya hitung hasilnya (dry run)
     * @return array{before: int, after: int, resized: bool}|null  null bila file tidak diproses
     */
    public static function optimize(string $path, bool $write = true): ?array
    {
        if (!is_file($path) || !in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), self::EXTENSIONS, true)) {
            return null;
        }

        $info = @getimagesize($path);

        if (!$info || !self::fitsInMemory($info[0], $info[1])) {
            return null;
        }

        $before = filesize($path);

        try {
            $image = (new ImageManager(new Driver()))->read($path);

            if ($image->isAnimated()) {
                return null;
            }

            $resized = max($image->width(), $image->height()) > self::MAX_DIMENSION;

            if ($resized) {
                $image->scaleDown(self::MAX_DIMENSION, self::MAX_DIMENSION);
            }

            $encoded = match (strtolower(pathinfo($path, PATHINFO_EXTENSION))) {
                'png' => $image->toPng(),
                'webp' => $image->toWebp(quality: self::QUALITY),
                default => $image->toJpeg(quality: self::QUALITY, progressive: true),
            };

            $data = (string) $encoded;
        } catch (\Throwable $e) {
            report($e);

            return null;
        }

        if ($data === '' || strlen($data) > $before * (1 - self::MIN_SAVING)) {
            return ['before' => $before, 'after' => $before, 'resized' => false];
        }

        if (!$write) {
            return ['before' => $before, 'after' => strlen($data), 'resized' => $resized];
        }

        // tulis ke file sementara lalu rename agar file tidak pernah setengah tertulis
        $temporary = $path . '.optimizing';

        if (file_put_contents($temporary, $data) === false || !rename($temporary, $path)) {
            @unlink($temporary);

            return null;
        }

        clearstatcache(true, $path);

        return ['before' => $before, 'after' => filesize($path), 'resized' => $resized];
    }

    /**
     * GD memakai ~5 byte per piksel; lewati gambar yang bisa melewati memory_limit.
     */
    private static function fitsInMemory(int $width, int $height): bool
    {
        $limit = self::bytes((string) ini_get('memory_limit'));

        if ($limit <= 0) {
            return true; // tanpa batas
        }

        $needed = $width * $height * 5 * 1.8; // gambar asli + hasil resize/encode

        return $needed < ($limit - memory_get_usage(true)) * 0.8;
    }

    private static function bytes(string $value): int
    {
        $value = trim($value);

        if ($value === '' || $value === '-1') {
            return -1;
        }

        $number = (int) $value;

        return match (strtolower(substr($value, -1))) {
            'g' => $number * 1024 ** 3,
            'm' => $number * 1024 ** 2,
            'k' => $number * 1024,
            default => $number,
        };
    }
}

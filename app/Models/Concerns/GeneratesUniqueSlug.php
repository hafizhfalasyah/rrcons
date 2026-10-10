<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

trait GeneratesUniqueSlug
{
    /**
     * Slug yang tidak boleh dipakai karena bentrok dengan route lain.
     */
    protected static function reservedSlugs(): array
    {
        return [];
    }

    /**
     * Buat slug unik dari $source. Bila sudah dipakai, ditambah akhiran -2, -3, dst.
     */
    public static function uniqueSlug(string $source, ?int $ignoreId = null): string
    {
        $base = rtrim(Str::limit(Str::slug($source), 240, ''), '-');

        if ($base === '') {
            $base = Str::slug(class_basename(static::class));
        }

        $slug = $base;
        $suffix = 2;

        while (
            in_array($slug, static::reservedSlugs(), true)
            || static::query()
                ->where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
                ->exists()
        ) {
            $slug = $base . '-' . $suffix++;
        }

        return $slug;
    }
}

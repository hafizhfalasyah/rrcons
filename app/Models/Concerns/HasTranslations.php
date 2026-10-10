<?php

namespace App\Models\Concerns;

/**
 * Konten bilingual disimpan sebagai <field>_id (Bahasa Indonesia, wajib) dan
 * <field>_en (English, opsional). Model mendaftarkan field-nya di $translatable,
 * lalu $model->field otomatis mengikuti locale aktif dengan fallback ke Indonesia.
 *
 * Catatan: untuk menyimpan, selalu isi kolom fisiknya (field_id / field_en).
 */
trait HasTranslations
{
    public const LOCALES = ['id', 'en'];

    public function getAttribute($key)
    {
        if (
            in_array($key, $this->translatable ?? [], true)
            // query bisa saja sudah meng-alias kolom, mis. "title_en as title"
            && !array_key_exists($key, $this->attributes)
        ) {
            return $this->translate($key);
        }

        return parent::getAttribute($key);
    }

    public function translate(string $field, ?string $locale = null): ?string
    {
        $locale ??= app()->getLocale();

        $value = $this->attributes["{$field}_{$locale}"] ?? null;

        if ($value === null || $value === '') {
            $value = $this->attributes["{$field}_id"] ?? null;
        }

        return $value;
    }

    /**
     * Kolom fisik untuk sebuah field, mis. title => [title_id, title_en].
     */
    public static function translationColumns(string $field): array
    {
        return array_map(fn ($locale) => "{$field}_{$locale}", self::LOCALES);
    }
}

<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;

/**
 * Validasi & pemetaan field bilingual <field>_id (Indonesia) dan <field>_en (English).
 */
trait ValidatesTranslations
{
    /**
     * ['title' => 'required|string|max:255'] menjadi
     * ['title_id' => 'required|string|max:255', 'title_en' => 'nullable|string|max:255'].
     * Versi English selalu opsional karena tampilan publik fallback ke Bahasa Indonesia.
     */
    protected function translatedRules(array $rules): array
    {
        $result = [];

        foreach ($rules as $field => $rule) {
            $parts = is_array($rule) ? $rule : explode('|', $rule);

            $result["{$field}_id"] = $parts;
            $result["{$field}_en"] = array_merge(
                ['nullable'],
                array_values(array_filter($parts, fn ($part) => !in_array($part, ['required', 'nullable'], true)))
            );
        }

        return $result;
    }

    /**
     * Validasi field bilingual + field biasa, lalu kembalikan data siap simpan.
     * Field English yang dikosongkan disimpan sebagai null (bukan diabaikan).
     */
    protected function validateWithTranslations(Request $request, array $translated, array $plain = []): array
    {
        $data = $request->validate($this->translatedRules($translated) + $plain);

        foreach (array_keys($translated) as $field) {
            $data["{$field}_en"] = $data["{$field}_en"] ?? null;
        }

        return $data;
    }
}

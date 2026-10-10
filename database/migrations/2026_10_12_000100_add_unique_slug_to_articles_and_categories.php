<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Slug yang bentrok dengan route admin /blog/{articles|categories|configs}.
     */
    private array $reserved = [
        'articles' => ['articles', 'categories', 'configs'],
        'categories' => [],
    ];

    /**
     * Run the migrations.
     *
     * Slug duplikat sebelumnya membuat artikel/kategori kedua tidak bisa diakses.
     * Baris tertua tetap memakai slug aslinya (URL yang sudah hidup tidak berubah),
     * sisanya diberi akhiran "-{id}", lalu unique index ditambahkan supaya tidak terulang.
     */
    public function up(): void
    {
        foreach ($this->reserved as $table => $reserved) {
            $rows = DB::table($table)->orderBy('id')->get(['id', 'slug']);

            // slug => id pemilik sah (baris tertua dengan slug valid)
            $taken = [];
            foreach ($rows as $row) {
                $slug = (string) $row->slug;

                if ($slug !== '' && !in_array($slug, $reserved, true) && !isset($taken[$slug])) {
                    $taken[$slug] = $row->id;
                }
            }

            foreach ($rows as $row) {
                $slug = (string) $row->slug;

                if (isset($taken[$slug]) && $taken[$slug] === $row->id) {
                    continue;
                }

                $newSlug = ($slug === '' ? Str::singular($table) : $slug) . '-' . $row->id;

                while (isset($taken[$newSlug])) {
                    $newSlug .= '-' . $row->id;
                }

                $taken[$newSlug] = $row->id;
                DB::table($table)->where('id', $row->id)->update(['slug' => $newSlug]);
            }

            Schema::table($table, function (Blueprint $table) {
                $table->unique('slug');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach (array_keys($this->reserved) as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropUnique(['slug']);
            });
        }
    }
};

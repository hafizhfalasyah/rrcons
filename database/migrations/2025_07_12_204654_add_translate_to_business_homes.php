<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private string $table = 'business_homes';

    /**
     * Kolom => tipe. Kolom lama di-rename menjadi <kolom>_id dan ditambah <kolom>_en.
     */
    private array $fields = [
        'title' => 'string',
        'desc' => 'string',
    ];

    /**
     * Run the migrations.
     *
     * Idempoten: rename & penambahan kolom hanya dilakukan bila belum ada, karena di
     * beberapa database perubahan ini sudah dilakukan manual tanpa tercatat.
     */
    public function up(): void
    {
        // Satu rename per Schema::table: SQLite tidak mendukung beberapa rename sekaligus
        foreach (array_keys($this->fields) as $field) {
            if (Schema::hasColumn($this->table, $field) && !Schema::hasColumn($this->table, "{$field}_id")) {
                Schema::table($this->table, function (Blueprint $table) use ($field) {
                    $table->renameColumn($field, "{$field}_id");
                });
            }
        }

        // Kolom baru ditambahkan setelah rename agar after() merujuk kolom yang sudah ada
        foreach ($this->fields as $field => $type) {
            if (!Schema::hasColumn($this->table, "{$field}_en")) {
                Schema::table($this->table, function (Blueprint $table) use ($field, $type) {
                    $table->{$type}("{$field}_en")->nullable()->after("{$field}_id");
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach (array_keys($this->fields) as $field) {
            if (Schema::hasColumn($this->table, "{$field}_en")) {
                Schema::table($this->table, function (Blueprint $table) use ($field) {
                    $table->dropColumn("{$field}_en");
                });
            }

            if (Schema::hasColumn($this->table, "{$field}_id") && !Schema::hasColumn($this->table, $field)) {
                Schema::table($this->table, function (Blueprint $table) use ($field) {
                    $table->renameColumn("{$field}_id", $field);
                });
            }
        }
    }
};

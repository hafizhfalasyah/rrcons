<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel => [kolom => tipe]. Setiap kolom dipecah menjadi <kolom>_id (isi lama,
     * Bahasa Indonesia) dan <kolom>_en (English, opsional - fallback ke _id).
     */
    private array $columns = [
        'articles'          => ['title' => 'string', 'desc' => 'longText'],
        'categories'        => ['name' => 'string'],
        'contact_menus'     => ['title' => 'string', 'sub_title' => 'string'],
        'rrfuture_menus'    => ['title' => 'string', 'sub_title' => 'string'],
        'service_menus'     => ['title' => 'string', 'sub_title' => 'string'],
        'us_contacts'       => ['desc' => 'string'],
        'gallery_homes'     => ['title' => 'string', 'desc' => 'string'],
        'sosmed_homes'      => ['title' => 'string'],
        'poster_homes'      => ['name' => 'string'],
        'profile_homes'     => ['position' => 'string'],
        'product_rrfutures' => ['name' => 'string', 'desc' => 'text'],
        'our_services'      => ['name' => 'string', 'desc' => 'text'],
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->columns as $tableName => $fields) {
            // Satu rename per Schema::table: SQLite tidak mendukung beberapa rename sekaligus
            foreach (array_keys($fields) as $field) {
                Schema::table($tableName, function (Blueprint $table) use ($field) {
                    $table->renameColumn($field, "{$field}_id");
                });
            }

            Schema::table($tableName, function (Blueprint $table) use ($fields) {
                foreach ($fields as $field => $type) {
                    $table->{$type}("{$field}_en")->nullable()->after("{$field}_id");
                }
            });
        }

        // Form mengizinkan field ini kosong, tetapi kolomnya NOT NULL sehingga
        // menyimpan tanpa deskripsi/link sebelumnya gagal dengan error SQL.
        Schema::table('gallery_homes', function (Blueprint $table) {
            $table->string('desc_id')->nullable()->change();
        });

        Schema::table('sosmed_homes', function (Blueprint $table) {
            $table->string('sosmed_link')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ($this->columns as $tableName => $fields) {
            Schema::table($tableName, function (Blueprint $table) use ($fields) {
                $table->dropColumn(array_map(fn ($field) => "{$field}_en", array_keys($fields)));
            });

            foreach (array_keys($fields) as $field) {
                Schema::table($tableName, function (Blueprint $table) use ($field) {
                    $table->renameColumn("{$field}_id", $field);
                });
            }
        }
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Judul & deskripsi khusus mesin pencari per bahasa. Opsional:
     * bila kosong dipakai judul artikel dan cuplikan isinya.
     */
    public function up(): void
    {
        if (Schema::hasColumn('articles', 'meta_title_id')) {
            return;
        }

        Schema::table('articles', function (Blueprint $table) {
            $table->string('meta_title_id')->nullable()->after('desc_en');
            $table->string('meta_title_en')->nullable()->after('meta_title_id');
            $table->string('meta_description_id', 320)->nullable()->after('meta_title_en');
            $table->string('meta_description_en', 320)->nullable()->after('meta_description_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn(['meta_title_id', 'meta_title_en', 'meta_description_id', 'meta_description_en']);
        });
    }
};

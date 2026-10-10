<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('advantage_homes', function (Blueprint $table) {
            $table->renameColumn('title', 'title_id');
        });

        Schema::table('advantage_homes', function (Blueprint $table) {
            $table->renameColumn('desc', 'desc_id');
        });

        Schema::table('advantage_homes', function (Blueprint $table) {
            $table->string('title_en')->nullable()->after('title_id');
            $table->text('desc_en')->nullable()->after('desc_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('advantage_homes', function (Blueprint $table) {
            $table->dropColumn(['title_en', 'desc_en']);
        });

        Schema::table('advantage_homes', function (Blueprint $table) {
            $table->renameColumn('title_id', 'title');
        });

        Schema::table('advantage_homes', function (Blueprint $table) {
            $table->renameColumn('desc_id', 'desc');
        });
    }
};

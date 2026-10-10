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
        Schema::table('about_homes', function (Blueprint $table) {
            $table->renameColumn('text', 'text_id');
        });

        Schema::table('about_homes', function (Blueprint $table) {
            $table->text('text_en')->nullable()->after('text_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('about_homes', function (Blueprint $table) {
            $table->dropColumn(['text_en']);
        });

        Schema::table('about_homes', function (Blueprint $table) {
            $table->renameColumn('text_id', 'text');
        });
    }
};

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
        Schema::table('home_menus', function (Blueprint $table) {
            $table->renameColumn('title', 'title_id');
        });

        Schema::table('home_menus', function (Blueprint $table) {
            $table->renameColumn('sub_title', 'sub_title_id');
        });

        Schema::table('home_menus', function (Blueprint $table) {
            $table->string('title_en')->nullable()->after('title_id');
            $table->string('sub_title_en')->nullable()->after('sub_title_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('home_menus', function (Blueprint $table) {
            $table->dropColumn(['title_en', 'sub_title_en']);
        });

        Schema::table('home_menus', function (Blueprint $table) {
            $table->renameColumn('title_id', 'title');
        });

        Schema::table('home_menus', function (Blueprint $table) {
            $table->renameColumn('sub_title_id', 'sub_title');
        });
    }
};

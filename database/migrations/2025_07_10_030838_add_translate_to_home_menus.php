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
            $table->renameColumn('sub_title', 'sub_title_id');
            $table->string('title_en')->nullable()->after('title');
            $table->string('sub_title_en')->nullable()->after('sub_title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('home_menus', function (Blueprint $table) {

        });
    }
};

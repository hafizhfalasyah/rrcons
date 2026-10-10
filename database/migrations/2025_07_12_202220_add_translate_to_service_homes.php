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
        Schema::table('service_homes', function (Blueprint $table) {
            $table->renameColumn('name', 'name_id');
        });

        Schema::table('service_homes', function (Blueprint $table) {
            $table->renameColumn('desc', 'desc_id');
        });

        Schema::table('service_homes', function (Blueprint $table) {
            $table->string('name_en')->nullable()->after('name_id');
            $table->text('desc_en')->nullable()->after('desc_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_homes', function (Blueprint $table) {
            $table->dropColumn(['name_en', 'desc_en']);
        });

        Schema::table('service_homes', function (Blueprint $table) {
            $table->renameColumn('name_id', 'name');
        });

        Schema::table('service_homes', function (Blueprint $table) {
            $table->renameColumn('desc_id', 'desc');
        });
    }
};

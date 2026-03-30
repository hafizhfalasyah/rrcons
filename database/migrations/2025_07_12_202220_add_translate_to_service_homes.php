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
            $table->renameColumn('desc', 'desc_id');
            $table->string('name_en')->nullable()->after('name');
            $table->text('desc_en')->nullable()->after('desc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_homes', function (Blueprint $table) {
            //
        });
    }
};

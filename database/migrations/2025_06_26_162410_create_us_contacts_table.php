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
        Schema::create('us_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_menu_id')
                  ->constrained('contact_menus')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            $table->string('title');
            $table->string('desc');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('us_contacts');
    }
};

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
        Schema::create('gallery_homes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('home_menu_id')
                  ->constrained('home_menus')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            $table->string('img');
            $table->string('title');
            $table->string('desc');
            $table->unsignedTinyInteger('month');
            $table->year('year');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gallery_homes');
    }
};

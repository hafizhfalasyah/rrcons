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
        Schema::create('product_rrfutures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rrfuture_menu_id')
                  ->constrained('rrfuture_menus')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            $table->string('name');
            $table->text('desc');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_rrfutures');
    }
};

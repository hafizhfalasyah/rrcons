<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * publish_date sebelumnya bertipe DATE dengan default statis (tanggal saat
     * migrasi dijalankan), sehingga penjadwalan tidak bisa sampai ke jam/menit.
     * Data lama otomatis menjadi "YYYY-MM-DD 00:00:00" sehingga tetap terbit.
     */
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dateTime('publish_date')->nullable()->default(null)->change();
        });

        // Migrasi ini dirilis bersama pergantian timezone UTC -> Asia/Jakarta. Penanda ini
        // memberi tahu migrasi koreksi timestamp lama bahwa pergantian terjadi di run ini.
        app()->instance('rrcons.timezone_switched_at', now());
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->date('publish_date')->nullable()->default(null)->change();
        });
    }
};

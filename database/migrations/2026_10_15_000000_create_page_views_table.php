<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Satu baris = satu tampilan halaman oleh browser sungguhan (terverifikasi JavaScript).
     * Tidak menyimpan IP mentah; visitor_hash berganti setiap hari.
     */
    public function up(): void
    {
        Schema::create('page_views', function (Blueprint $table) {
            $table->id();
            $table->uuid('view_id')->nullable()->unique();
            $table->char('visitor_hash', 64);
            $table->string('path');
            $table->string('route_name', 64)->nullable();
            $table->foreignId('article_id')->nullable()->constrained('articles')->nullOnDelete();
            $table->string('locale', 5)->nullable();
            $table->string('referrer_host')->nullable();
            $table->string('source', 16)->default('direct');
            $table->string('utm_source', 100)->nullable();
            $table->string('utm_medium', 100)->nullable();
            $table->string('utm_campaign', 100)->nullable();
            $table->string('device', 16)->nullable();
            $table->string('browser', 32)->nullable();
            $table->string('os', 32)->nullable();
            $table->unsignedSmallInteger('screen_width')->nullable();
            $table->string('browser_language', 16)->nullable();
            $table->char('country', 2)->nullable();
            $table->unsignedInteger('engaged_seconds')->default(0);
            // 'js' = dicatat browser, 'legacy' = disalin dari tabel visitors lama
            $table->string('tracker', 10)->default('js');
            $table->unsignedBigInteger('legacy_visitor_id')->nullable()->unique();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('last_seen_at')->nullable();

            $table->index('created_at');
            $table->index('last_seen_at');
            $table->index(['visitor_hash', 'created_at']);
            $table->index(['article_id', 'created_at']);
            $table->index(['path', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_views');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Slug lama artikel. Saat judul (dan slug) artikel berubah, URL lama dialihkan
     * permanen (301) ke URL terbaru sehingga link yang sudah tersebar & hasil
     * mesin pencari tidak berubah menjadi 404.
     */
    public function up(): void
    {
        if (!Schema::hasTable('article_slug_redirects')) {
            Schema::create('article_slug_redirects', function (Blueprint $table) {
                $table->id();
                $table->foreignId('article_id')->constrained('articles')->cascadeOnDelete();
                $table->string('slug')->unique();
                $table->timestamps();
            });
        }

        // /blog/comments kini dipakai halaman moderasi komentar di panel admin
        DB::table('articles')->where('slug', 'comments')->orderBy('id')->get(['id'])->each(function ($article) {
            DB::table('articles')->where('id', $article->id)->update(['slug' => 'comments-' . $article->id]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_slug_redirects');
    }
};

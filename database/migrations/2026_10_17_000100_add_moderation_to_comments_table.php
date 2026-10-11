<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Moderasi komentar blog: komentar baru berstatus "pending" dan baru tampil di website
     * setelah disetujui admin/penulis. Komentar yang sudah ada sebelumnya sudah tampil,
     * jadi ditandai "approved" agar tidak ada yang hilang dari halaman artikel.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('comments', 'status')) {
            Schema::table('comments', function (Blueprint $table) {
                $table->string('status', 16)->default('pending')->after('content');
                $table->foreignId('moderated_by')->nullable()->after('status')->constrained('users')->nullOnDelete();
                $table->timestamp('moderated_at')->nullable()->after('moderated_by');
                $table->index(['article_id', 'status'], 'comments_article_id_status_index');
                $table->index(['status', 'created_at'], 'comments_status_created_at_index');
            });

            DB::table('comments')->update(['status' => 'approved']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('comments', 'status')) {
            Schema::table('comments', function (Blueprint $table) {
                $table->dropIndex('comments_article_id_status_index');
                $table->dropIndex('comments_status_created_at_index');
                $table->dropConstrainedForeignId('moderated_by');
                $table->dropColumn(['status', 'moderated_at']);
            });
        }
    }
};

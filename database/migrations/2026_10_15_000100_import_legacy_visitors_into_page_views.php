<?php

use App\Models\PageView;
use App\Support\Analytics\LegacyVisitorImporter;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Salin riwayat kunjungan lama (tabel visitors, tetap utuh) ke page_views tanpa bot.
     */
    public function up(): void
    {
        $report = LegacyVisitorImporter::import();

        Log::info('Legacy visitors imported into page_views', $report);
    }

    /**
     * Reverse the migrations: hapus hanya salinan legacy, data visitors tidak tersentuh.
     */
    public function down(): void
    {
        DB::table('page_views')->where('tracker', PageView::TRACKER_LEGACY)->delete();
    }
};

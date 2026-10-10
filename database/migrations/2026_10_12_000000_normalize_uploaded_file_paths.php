<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    /**
     * Tabel => [kolom, direktori di disk public].
     */
    private array $columns = [
        'articles'       => ['img', 'back/blog'],
        'poster_homes'   => ['img', 'back/home/poster'],
        'hero_homes'     => ['bg_img', 'back/home/hero'],
        'about_homes'    => ['img', 'back/home/about'],
        'profile_homes'  => ['img', 'back/home/profile'],
        'business_homes' => ['img', 'back/home/business'],
        'sosmed_homes'   => ['video', 'back/home/sosmed'],
        'gallery_homes'  => ['img', 'back/home/gallery'],
        'hero_rrfutures' => ['bg_img', 'back/rrfuture/hero'],
        'hero_services'  => ['bg_img', 'back/service/hero'],
        'hero_contacts'  => ['bg_img', 'back/contact/hero'],
    ];

    /**
     * Run the migrations.
     *
     * Data lama hanya menyimpan nama file ("abc.jpg"), sedangkan view sekarang
     * memakai asset('storage/' . $path). Migrasi ini mengubahnya menjadi path
     * relatif terhadap disk public ("back/home/hero/abc.jpg"). Aman dijalankan
     * berulang karena nilai yang sudah berisi "/" dilewati.
     */
    public function up(): void
    {
        $disk = Storage::disk('public');

        foreach ($this->columns as $table => [$column, $directory]) {
            if (!Schema::hasTable($table) || !Schema::hasColumn($table, $column)) {
                continue;
            }

            DB::table($table)
                ->whereNotNull($column)
                ->where($column, '<>', '')
                ->where($column, 'not like', '%/%')
                ->orderBy('id')
                ->each(function ($row) use ($table, $column, $directory, $disk) {
                    $path = $directory . '/' . $row->{$column};

                    // File yang ter-upload saat FILESYSTEM_DISK=public dengan kode lama
                    // tersimpan di "public/<direktori>/..." di dalam disk public.
                    $misplaced = 'public/' . $path;
                    if (!$disk->exists($path) && $disk->exists($misplaced)) {
                        $disk->move($misplaced, $path);
                    }

                    DB::table($table)->where('id', $row->id)->update([$column => $path]);
                });
        }
    }

    /**
     * Reverse the migrations.
     *
     * Tidak dikembalikan: path lengkap tetap valid untuk kode saat ini.
     */
    public function down(): void
    {
        //
    }
};

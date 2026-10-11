<?php

namespace App\Support;

use App\Support\Analytics\LegacyVisitorImporter;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Mengoreksi timestamp yang tersimpan saat timezone aplikasi masih UTC.
 *
 * Sebelum timezone diganti ke Asia/Jakarta, Laravel menyimpan created_at, updated_at,
 * dll. dalam UTC. Setelah diganti, nilai itu dibaca sebagai WIB sehingga tampil 7 jam
 * lebih awal. Kelas ini menggeser nilai era-UTC ke waktu lokal.
 *
 * Pengaman:
 * - Setiap nilai lama disalin ke tabel legacy_timezone_backups sebelum diubah
 *   (bisa dipulihkan dengan restore()). Tidak ada baris yang dihapus.
 * - Baris yang sudah tercatat di backup dilewati, jadi aman dijalankan ulang.
 * - Hanya nilai <= batas era-UTC yang digeser; data yang ditulis setelah timezone
 *   diganti (sudah WIB) tidak disentuh. Penilaian per kolom.
 */
class LegacyTimezoneFix
{
    public const BACKUP_TABLE = 'legacy_timezone_backups';

    /** Tabel aplikasi yang punya timestamp */
    private const TABLES = [
        'users', 'categories', 'articles', 'comments', 'configs', 'visitors',
        'home_menus', 'hero_homes', 'about_homes', 'service_homes', 'advantage_homes', 'profile_homes',
        'gallery_homes', 'business_homes', 'sosmed_homes', 'poster_homes',
        'rrfuture_menus', 'hero_rrfutures', 'product_rrfutures',
        'service_menus', 'hero_services', 'our_services',
        'contact_menus', 'hero_contacts', 'us_contacts',
    ];

    private const TIMESTAMP_COLUMNS = ['created_at', 'updated_at', 'email_verified_at'];

    private array $report = [];

    /**
     * @param  Carbon  $switchedAt  Waktu (zona aplikasi) ketika versi ber-timezone Asia/Jakarta mulai berjalan.
     */
    public function __construct(private Carbon $switchedAt, private bool $dryRun = false)
    {
    }

    /**
     * Selisih menit antara zona aplikasi dan UTC (Asia/Jakarta = 420).
     */
    public static function offsetMinutes(): int
    {
        return (int) now()->utcOffset();
    }

    /**
     * Nilai yang ditulis di era UTC paling besar bernilai "waktu switch dalam UTC".
     */
    public function threshold(): string
    {
        return $this->switchedAt->copy()->subMinutes(self::offsetMinutes())->format('Y-m-d H:i:s');
    }

    public function run(): array
    {
        $this->report = [];

        if (self::offsetMinutes() === 0) {
            return $this->report; // aplikasi masih UTC, tidak ada yang perlu digeser
        }

        if (!$this->dryRun) {
            $this->ensureBackupTable();
        }

        foreach (self::TABLES as $table) {
            if (!Schema::hasTable($table)) {
                continue;
            }

            // publish_date & visit_date diturunkan dari nilai created_at/updated_at ASLI,
            // jadi harus diproses sebelum kolom timestamp digeser.
            if ($table === 'articles' && Schema::hasColumn($table, 'publish_date')) {
                $this->fixPublishDates();
            }

            if ($table === 'visitors' && Schema::hasColumn($table, 'visit_date')) {
                $this->fixVisitDates();
            }

            foreach (self::TIMESTAMP_COLUMNS as $column) {
                if (Schema::hasColumn($table, $column)) {
                    $this->shiftColumn($table, $column);
                }
            }
        }

        // salinan riwayat kunjungan di page_views mengikuti tabel visitors
        if (!$this->dryRun) {
            $this->report['page_views.legacy_synced'] = LegacyVisitorImporter::syncTimestamps();
        }

        return $this->report;
    }

    /**
     * Kembalikan semua nilai dari backup (hanya bila belum diubah lagi sejak dikoreksi).
     */
    public static function restore(): array
    {
        $report = [];

        if (!Schema::hasTable(self::BACKUP_TABLE)) {
            return $report;
        }

        DB::table(self::BACKUP_TABLE)->orderBy('id')->chunkById(500, function ($rows) use (&$report) {
            foreach ($rows as $row) {
                $restored = DB::table($row->table_name)
                    ->where('id', $row->record_id)
                    ->where($row->column_name, $row->new_value)
                    ->update([$row->column_name => $row->old_value]);

                if ($restored) {
                    $key = $row->table_name . '.' . $row->column_name;
                    $report[$key] = ($report[$key] ?? 0) + 1;
                }

                DB::table(self::BACKUP_TABLE)->where('id', $row->id)->delete();
            }
        });

        $report['page_views.legacy_synced'] = LegacyVisitorImporter::syncTimestamps();

        return $report;
    }

    private function shiftColumn(string $table, string $column): void
    {
        $this->process($table, $column, function ($row) use ($column) {
            return Carbon::parse($row->{$column})->addMinutes(self::offsetMinutes())->format('Y-m-d H:i:s');
        }, fn ($query) => $query->where($column, '<=', $this->threshold()));
    }

    /**
     * Kode lama mengisi publish_date dengan tanggal (tanpa jam) saat artikel disimpan.
     * Waktu sebenarnya diambil dari created_at/updated_at yang tanggalnya sama, lalu
     * dikonversi ke waktu lokal.
     */
    private function fixPublishDates(): void
    {
        $this->process('articles', 'publish_date', function ($row) {
            $date = substr((string) $row->publish_date, 0, 10);

            foreach (['updated_at', 'created_at'] as $source) {
                if ($row->{$source} && substr((string) $row->{$source}, 0, 10) === $date
                    && (string) $row->{$source} <= $this->threshold()) {
                    return Carbon::parse($row->{$source})->addMinutes(self::offsetMinutes())->format('Y-m-d H:i:s');
                }
            }

            return null; // tidak ada pasangan yang cocok → biarkan
        }, fn ($query) => $query
            ->where('publish_date', '<=', $this->threshold())
            ->where(fn ($q) => $q->where('publish_date', 'like', '% 00:00:00')->orWhereRaw('length(publish_date) = 10')));
    }

    /**
     * visit_date dulu memakai tanggal UTC; hitung ulang dari created_at dalam waktu lokal.
     */
    private function fixVisitDates(): void
    {
        $this->process('visitors', 'visit_date', function ($row) {
            if (!$row->created_at || (string) $row->created_at > $this->threshold()) {
                return null;
            }

            return Carbon::parse($row->created_at)->addMinutes(self::offsetMinutes())->toDateString();
        }, fn ($query) => $query->whereNotNull('created_at')->where('created_at', '<=', $this->threshold()));
    }

    /**
     * Backup + update satu kolom, per baris, dalam transaksi per tabel.
     */
    private function process(string $table, string $column, callable $newValue, callable $scope): void
    {
        $key = $table . '.' . $column;
        $this->report[$key] ??= 0;

        $query = DB::table($table)->whereNotNull($column);

        // lewati baris yang sudah pernah dikoreksi
        if (Schema::hasTable(self::BACKUP_TABLE)) {
            $query->whereNotExists(function ($sub) use ($table, $column) {
                $sub->select(DB::raw(1))
                    ->from(self::BACKUP_TABLE)
                    ->where('table_name', $table)
                    ->where('column_name', $column)
                    ->whereColumn('record_id', $table . '.id');
            });
        }

        $scope($query);

        $query->orderBy('id')->chunkById(500, function ($rows) use ($table, $column, $newValue, $key) {
            DB::transaction(function () use ($rows, $table, $column, $newValue, $key) {
                foreach ($rows as $row) {
                    $value = $newValue($row);

                    if ($value === null || $value === (string) $row->{$column}) {
                        continue;
                    }

                    $this->report[$key]++;

                    if ($this->dryRun) {
                        continue;
                    }

                    DB::table(self::BACKUP_TABLE)->insert([
                        'table_name' => $table,
                        'record_id' => $row->id,
                        'column_name' => $column,
                        'old_value' => (string) $row->{$column},
                        'new_value' => $value,
                        'created_at' => now()->format('Y-m-d H:i:s'),
                    ]);

                    DB::table($table)->where('id', $row->id)->update([$column => $value]);
                }
            });
        });
    }

    private function ensureBackupTable(): void
    {
        if (Schema::hasTable(self::BACKUP_TABLE)) {
            return;
        }

        Schema::create(self::BACKUP_TABLE, function ($table) {
            $table->id();
            $table->string('table_name', 64);
            $table->unsignedBigInteger('record_id');
            $table->string('column_name', 64);
            $table->string('old_value', 32)->nullable();
            $table->string('new_value', 32)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->unique(['table_name', 'record_id', 'column_name'], 'legacy_tz_backup_unique');
        });
    }
}

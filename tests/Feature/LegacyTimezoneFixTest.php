<?php

namespace Tests\Feature;

use App\Support\LegacyTimezoneFix;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class LegacyTimezoneFixTest extends TestCase
{
    use RefreshDatabase;

    private Carbon $switchedAt;

    protected function setUp(): void
    {
        parent::setUp();

        // versi Asia/Jakarta mulai berjalan 11 Okt 2026 10:00 WIB (= 03:00 UTC)
        $this->switchedAt = Carbon::parse('2026-10-11 10:00:00', 'Asia/Jakarta');
        $this->travelTo($this->switchedAt->copy()->addHour());

        // data era UTC (ditulis aplikasi lama, nilai dalam UTC)
        DB::table('users')->insert(['id' => 1, 'name' => 'Admin', 'email' => 'a@a.a', 'password' => 'x', 'role' => 'admin', 'created_at' => '2025-08-01 20:30:00', 'updated_at' => '2025-08-01 20:30:00']);
        DB::table('categories')->insert(['id' => 1, 'name_id' => 'Berita', 'slug' => 'berita', 'created_at' => '2025-08-01 01:00:00', 'updated_at' => '2025-08-01 01:00:00']);
        DB::table('articles')->insert([
            ['id' => 1, 'user_id' => 1, 'category_id' => 1, 'title_id' => 'Satu', 'slug' => 'satu', 'desc_id' => 'a', 'img' => 'a.jpg', 'created_by' => 1, 'status' => '1',
                'publish_date' => '2025-08-03 00:00:00', 'created_at' => '2025-08-01 20:30:00', 'updated_at' => '2025-08-03 22:15:00'],
            // dibuat era UTC, diedit setelah pergantian (sudah WIB)
            ['id' => 2, 'user_id' => 1, 'category_id' => 1, 'title_id' => 'Dua', 'slug' => 'dua', 'desc_id' => 'b', 'img' => 'b.jpg', 'created_by' => 1, 'status' => '1',
                'publish_date' => '2026-10-11 10:30:00', 'created_at' => '2025-09-10 02:00:00', 'updated_at' => '2026-10-11 10:30:00'],
        ]);
        DB::table('visitors')->insert([
            ['id' => 1, 'ip_address' => '1.1.1.1', 'path' => '/', 'visit_date' => '2025-08-01', 'created_at' => '2025-08-01 18:00:00', 'updated_at' => '2025-08-01 18:00:00'],
            // ditulis setelah pergantian, nilai sudah WIB
            ['id' => 2, 'ip_address' => '2.2.2.2', 'path' => '/', 'visit_date' => '2026-10-11', 'created_at' => '2026-10-11 10:45:00', 'updated_at' => '2026-10-11 10:45:00'],
        ]);
    }

    public function test_it_shifts_only_utc_era_values_and_keeps_a_backup(): void
    {
        $report = (new LegacyTimezoneFix($this->switchedAt))->run();

        $this->assertSame('2025-08-02 03:30:00', DB::table('users')->value('created_at'));
        $this->assertSame('2025-08-01 08:00:00', DB::table('categories')->value('created_at'));

        $first = DB::table('articles')->find(1);
        $this->assertSame('2025-08-02 03:30:00', $first->created_at);
        $this->assertSame('2025-08-04 05:15:00', $first->updated_at);
        // publish_date tanpa jam diambil dari updated_at (tanggalnya sama), dalam WIB
        $this->assertSame('2025-08-04 05:15:00', $first->publish_date);

        $second = DB::table('articles')->find(2);
        $this->assertSame('2025-09-10 09:00:00', $second->created_at);
        $this->assertSame('2026-10-11 10:30:00', $second->updated_at, 'WIB-era values must not change');
        $this->assertSame('2026-10-11 10:30:00', $second->publish_date);

        $this->assertSame('2025-08-02', DB::table('visitors')->where('id', 1)->value('visit_date'));
        $this->assertSame('2026-10-11 10:45:00', DB::table('visitors')->where('id', 2)->value('created_at'));

        $this->assertSame(1, $report['articles.publish_date']);
        $this->assertDatabaseHas(LegacyTimezoneFix::BACKUP_TABLE, [
            'table_name' => 'articles', 'record_id' => 1, 'column_name' => 'created_at',
            'old_value' => '2025-08-01 20:30:00', 'new_value' => '2025-08-02 03:30:00',
        ]);
        $this->assertSame(2, DB::table('articles')->count(), 'no rows may be deleted');
    }

    public function test_running_twice_does_not_shift_again(): void
    {
        (new LegacyTimezoneFix($this->switchedAt))->run();
        $report = (new LegacyTimezoneFix($this->switchedAt))->run();

        $this->assertSame([], array_filter($report));
        $this->assertSame('2025-08-02 03:30:00', DB::table('users')->value('created_at'));
    }

    public function test_dry_run_changes_nothing(): void
    {
        $report = (new LegacyTimezoneFix($this->switchedAt, dryRun: true))->run();

        $this->assertGreaterThan(0, $report['users.created_at']);
        $this->assertSame('2025-08-01 20:30:00', DB::table('users')->value('created_at'));
    }

    public function test_restore_returns_the_original_values(): void
    {
        (new LegacyTimezoneFix($this->switchedAt))->run();
        LegacyTimezoneFix::restore();

        $this->assertSame('2025-08-01 20:30:00', DB::table('users')->value('created_at'));
        $this->assertSame('2025-08-03 00:00:00', DB::table('articles')->where('id', 1)->value('publish_date'));
        $this->assertSame('2025-08-01', DB::table('visitors')->where('id', 1)->value('visit_date'));
        $this->assertSame(0, DB::table(LegacyTimezoneFix::BACKUP_TABLE)->count());
    }

    public function test_artisan_command_supports_dry_run_and_requires_the_switch_time(): void
    {
        $this->artisan('rrcons:fix-legacy-timezone')->assertExitCode(2);

        $this->artisan('rrcons:fix-legacy-timezone', ['--switched-at' => '2026-10-11 10:00', '--dry-run' => true])
            ->expectsOutputToContain('Dry run')
            ->assertExitCode(0);
        $this->assertSame('2025-08-01 20:30:00', DB::table('users')->value('created_at'));

        $this->artisan('rrcons:fix-legacy-timezone', ['--switched-at' => '2026-10-11 10:00', '--force' => true])->assertExitCode(0);
        $this->assertSame('2025-08-02 03:30:00', DB::table('users')->value('created_at'));

        $this->artisan('rrcons:fix-legacy-timezone', ['--restore' => true, '--force' => true])->assertExitCode(0);
        $this->assertSame('2025-08-01 20:30:00', DB::table('users')->value('created_at'));
    }
}

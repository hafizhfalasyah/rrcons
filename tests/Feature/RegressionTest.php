<?php

namespace Tests\Feature;

use App\Models\Blog\Config;
use App\Models\Contact\ContactMenu;
use App\Models\Contact\HeroContact;
use App\Models\RRfuture\HeroRRfuture;
use App\Models\RRfuture\RRfutureMenu;
use App\Models\Service\HeroService;
use App\Models\Service\ServiceMenu;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RegressionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Blade memecah template dengan tokenizer PHP. Bila short_open_tag=On (bawaan image Docker
     * php:*), "<?" di template dibaca sebagai tag PHP sehingga sitemap.xml & feed error 500.
     */
    public function test_templates_never_contain_a_short_open_tag(): void
    {
        $offenders = [];
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(resource_path('views')));

        foreach ($iterator as $file) {
            if ($file->isFile() && str_ends_with($file->getFilename(), '.blade.php')
                && preg_match('/<\?(?!php\b)/', file_get_contents($file->getPathname()))) {
                $offenders[] = str_replace(base_path() . DIRECTORY_SEPARATOR, '', $file->getPathname());
            }
        }

        $this->assertSame([], $offenders);
    }

    public function test_sitemap_and_feed_start_with_the_xml_prolog(): void
    {
        foreach (['/sitemap.xml', '/feed'] as $uri) {
            $this->assertStringStartsWith('<?xml version="1.0" encoding="UTF-8"?>' . "\n", $this->get($uri)->assertOk()->getContent());
        }
    }

    public function test_every_hero_section_accepts_webp_images(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $heroes = [
            '/rrfuture/hero' => HeroRRfuture::create(['rrfuture_menu_id' => RRfutureMenu::create(['title_id' => 'RR'])->id]),
            '/service/hero' => HeroService::create(['service_menu_id' => ServiceMenu::create(['title_id' => 'Layanan'])->id]),
            '/contact/hero' => HeroContact::create(['contact_menu_id' => ContactMenu::create(['title_id' => 'Kontak'])->id]),
        ];

        foreach ($heroes as $uri => $hero) {
            $this->actingAs($admin)
                ->post("{$uri}/{$hero->id}/update", ['bg_img' => UploadedFile::fake()->image('hero.webp', 40, 20)])
                ->assertRedirect($uri)
                ->assertSessionHasNoErrors();

            $this->assertStringEndsWith('.webp', $hero->fresh()->bg_img);
            Storage::disk('public')->assertExists($hero->fresh()->bg_img);
        }
    }

    public function test_config_seeder_runs_and_can_be_repeated_without_duplicates(): void
    {
        $this->seed();
        Config::where('name', 'blogname')->update(['value' => 'diubah admin']);
        $this->seed();

        $this->assertSame(13, Config::count());
        $this->assertSame('diubah admin', Config::where('name', 'blogname')->value('value'));
    }
}

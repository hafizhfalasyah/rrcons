<?php

namespace Tests\Feature;

use App\Models\Blog\Category;
use App\Models\Home\AboutHome;
use App\Models\Home\HomeMenu;
use App\Models\Home\ServiceHome;
use App\Models\User;
use App\Support\ImageOptimizer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PerformanceTest extends TestCase
{
    use RefreshDatabase;

    private const PAGES = ['/', '/blog', '/contact', '/rrfuture', '/service'];

    public function test_public_pages_use_compiled_tailwind_instead_of_the_play_cdn(): void
    {
        $this->assertFileExists(public_path('front/css/app.css'));
        $this->assertGreaterThan(10000, filesize(public_path('front/css/app.css')));

        foreach (self::PAGES as $uri) {
            $html = $this->get($uri)->assertOk()->getContent();

            $this->assertStringNotContainsString('cdn.tailwindcss.com', $html, $uri);
            $this->assertMatchesRegularExpression('#<link rel="stylesheet" href="[^"]*front/css/app\.css\?v=\d+">\s*</head>#', $html, $uri . ': compiled CSS must be the last stylesheet in <head>');
            // pustaka yang tidak dipakai tidak lagi dimuat
            $this->assertStringNotContainsString('lottie-player', $html, $uri);
            $this->assertStringNotContainsString('flatpickr', $html, $uri);
            $this->assertStringNotContainsString('alpinejs@3.x.x', $html, $uri);
        }
    }

    public function test_compiled_css_contains_classes_used_by_views_and_scripts(): void
    {
        $css = file_get_contents(public_path('front/css/app.css'));

        // contoh class dari view, kondisi Blade, JavaScript & helper PHP
        foreach (['.-translate-x-full', '.translate-x-0', '.rotate-180', '.hidden', '.text-blue-500', '.rounded-\[2rem\]', '.hover\:text-blue-600:hover', '.md\:flex-row'] as $selector) {
            $this->assertStringContainsString($selector, $css, $selector . ' missing — run npm run build:front-css');
        }
    }

    public function test_route_names_are_unique_so_routes_can_be_cached(): void
    {
        $names = collect(Route::getRoutes()->getRoutes())->map->getName()->filter();

        $this->assertSame([], $names->duplicates()->values()->all(), 'php artisan route:cache fails on duplicate route names');
    }

    public function test_file_manager_is_only_reachable_through_the_role_protected_routes(): void
    {
        $uploader = User::factory()->create(['role' => User::ROLE_UPLOADER]);

        $this->actingAs($uploader)->get('/filemanager')->assertNotFound();
        $this->actingAs($uploader)->get('/laravel-filemanager')->assertForbidden();
    }

    public function test_home_page_loads_menus_once(): void
    {
        $menu = HomeMenu::create(['title_id' => 'Tentang', 'title_en' => 'About']);
        AboutHome::create(['home_menu_id' => $menu->id, 'img' => 'a.jpg', 'text_id' => 'Halo']);
        ServiceHome::create(['home_menu_id' => $menu->id, 'name_id' => 'Layanan', 'desc_id' => 'D']);

        $menuQueries = 0;
        DB::listen(function ($query) use (&$menuQueries) {
            if (str_contains($query->sql, 'from "home_menus"') || str_contains($query->sql, 'from `home_menus`')) {
                $menuQueries++;
            }
        });

        $this->withSession(['app_locale' => 'en'])->get('/')->assertOk()->assertSee('About')->assertSee('Halo');

        $this->assertSame(1, $menuQueries);
    }

    public function test_asset_version_appends_the_file_modification_time(): void
    {
        $this->assertSame(asset('front/css/app.css') . '?v=' . filemtime(public_path('front/css/app.css')), asset_version('front/css/app.css'));
        $this->assertSame(asset('missing.css'), asset_version('missing.css'));
    }

    public function test_image_optimizer_shrinks_large_images_without_changing_format(): void
    {
        $directory = storage_path('framework/testing/optimizer');
        @mkdir($directory, 0777, true);

        // foto besar dengan noise (JPEG kualitas tinggi), melebihi batas dimensi
        $jpeg = $directory . '/big.jpg';
        $image = imagecreatetruecolor(3000, 2000);
        for ($i = 0; $i < 4000; $i++) {
            imagefilledellipse($image, random_int(0, 3000), random_int(0, 2000), random_int(5, 80), random_int(5, 80), random_int(0, 0xFFFFFF));
        }
        imagejpeg($image, $jpeg, 100);
        imagedestroy($image);

        $result = ImageOptimizer::optimize($jpeg);

        $this->assertTrue($result['resized']);
        $this->assertLessThan($result['before'], $result['after']);
        [$width, $height, $type] = getimagesize($jpeg);
        $this->assertSame([2560, 1707, IMAGETYPE_JPEG], [$width, $height, $type]);

        // dry run tidak mengubah file
        $png = $directory . '/small.png';
        $image = imagecreatetruecolor(200, 200);
        imagepng($image, $png, 0); // tanpa kompresi
        imagedestroy($image);
        $size = filesize($png);
        $preview = ImageOptimizer::optimize($png, false);
        $this->assertLessThan($size, $preview['after']);
        $this->assertSame($size, filesize($png));

        ImageOptimizer::optimize($png);
        $this->assertSame(IMAGETYPE_PNG, getimagesize($png)[2]);
        $this->assertLessThan($size, filesize($png));

        // bukan gambar / tidak didukung -> dilewati
        file_put_contents($directory . '/note.txt', 'x');
        $this->assertNull(ImageOptimizer::optimize($directory . '/note.txt'));
        $this->assertNull(ImageOptimizer::optimize($directory . '/missing.jpg'));

        array_map('unlink', glob($directory . '/*'));
        @rmdir($directory);
    }

    public function test_uploaded_images_are_optimized_automatically(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $category = Category::create(['name_id' => 'Berita', 'slug' => 'berita']);

        $upload = UploadedFile::fake()->image('cover.jpg', 3200, 1800);

        $this->actingAs($admin)->post(route('blog.articles.store'), [
            'title_id' => 'Optimasi',
            'category_id' => $category->id,
            'desc_id' => '<p>Isi</p>',
            'img' => $upload,
            'publication' => 'publish',
        ])->assertRedirect();

        $path = DB::table('articles')->value('img');
        [$width] = getimagesize(Storage::disk('public')->path($path));

        $this->assertSame(ImageOptimizer::MAX_DIMENSION, $width);
    }
}

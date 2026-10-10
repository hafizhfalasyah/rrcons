<?php

namespace Tests\Feature;

use App\Models\Blog\Article;
use App\Models\Blog\Category;
use App\Models\Blog\Config;
use App\Models\Contact\ContactMenu;
use App\Models\Contact\HeroContact;
use App\Models\Contact\UsContact;
use App\Models\Home\AboutHome;
use App\Models\Home\AdvantageHome;
use App\Models\Home\BusinessHome;
use App\Models\Home\GalleryHome;
use App\Models\Home\HeroHome;
use App\Models\Home\HomeMenu;
use App\Models\Home\PosterHome;
use App\Models\Home\ProfileHome;
use App\Models\Home\ServiceHome;
use App\Models\Home\SosmedHome;
use App\Models\RRfuture\HeroRRfuture;
use App\Models\RRfuture\ProductRRfuture;
use App\Models\RRfuture\RRfutureMenu;
use App\Models\Service\HeroService;
use App\Models\Service\OurService;
use App\Models\Service\ServiceMenu;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MultiLanguageTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    }

    /**
     * Satu baris contoh untuk setiap section CMS (bilingual).
     */
    private function seedCms(): void
    {
        $menu = HomeMenu::create(['title_id' => 'Menu Beranda', 'title_en' => 'Home Menu EN', 'sub_title_id' => 'Sub', 'sub_title_en' => 'Sub EN']);
        HeroHome::create(['home_menu_id' => $menu->id, 'bg_img' => 'back/home/hero/a.jpg']);
        AboutHome::create(['home_menu_id' => $menu->id, 'img' => 'back/home/about/a.jpg', 'text_id' => 'Tentang kami', 'text_en' => 'About us']);
        foreach ([1, 2, 3, 4] as $i) {
            ServiceHome::create(['home_menu_id' => $menu->id, 'name_id' => "Layanan $i", 'name_en' => "Service $i", 'desc_id' => 'Deskripsi', 'desc_en' => 'Description']);
        }
        AdvantageHome::create(['home_menu_id' => $menu->id, 'title_id' => 'Keunggulan', 'title_en' => 'Advantage EN', 'desc_id' => 'D', 'desc_en' => 'D EN']);
        ProfileHome::create(['home_menu_id' => $menu->id, 'img' => 'back/home/profile/a.jpg', 'name' => 'Rita', 'position_id' => 'Direktur', 'position_en' => 'Director']);
        BusinessHome::create(['home_menu_id' => $menu->id, 'img' => 'back/home/business/a.jpg', 'title_id' => 'Pelatihan', 'title_en' => 'Training', 'desc_id' => 'D', 'form_link' => 'https://example.com']);
        SosmedHome::create(['home_menu_id' => $menu->id, 'video' => 'back/home/sosmed/a.mp4', 'title_id' => 'Video kami', 'title_en' => 'Our video', 'sosmed_link' => 'https://example.com']);
        GalleryHome::create(['home_menu_id' => $menu->id, 'img' => 'back/home/gallery/a.jpg', 'title_id' => 'Acara', 'title_en' => 'Event', 'month' => 1, 'year' => 2025]);
        PosterHome::create(['img' => 'back/home/poster/a.jpg', 'name_id' => 'Poster ID', 'name_en' => 'Poster EN']);

        $rrMenu = RRfutureMenu::create(['title_id' => 'Produk Kami', 'title_en' => 'Our Products']);
        HeroRRfuture::create(['rrfuture_menu_id' => $rrMenu->id, 'bg_img' => 'back/rrfuture/hero/a.jpg']);
        ProductRRfuture::create(['rrfuture_menu_id' => $rrMenu->id, 'name_id' => 'Produk Satu', 'name_en' => 'Product One', 'desc_id' => 'D', 'desc_en' => 'D EN']);

        $serviceMenu = ServiceMenu::create(['title_id' => 'Layanan Kami', 'title_en' => 'Our Services']);
        HeroService::create(['service_menu_id' => $serviceMenu->id, 'bg_img' => 'back/service/hero/a.jpg']);
        OurService::create(['service_menu_id' => $serviceMenu->id, 'name_id' => 'Konsultasi', 'name_en' => 'Consulting', 'desc_id' => 'D', 'desc_en' => 'D EN']);

        $contactMenu = ContactMenu::create(['title_id' => 'Hubungi Kami', 'title_en' => 'Contact Us EN']);
        HeroContact::create(['contact_menu_id' => $contactMenu->id, 'bg_img' => 'back/contact/hero/a.jpg']);
        UsContact::create(['contact_menu_id' => $contactMenu->id, 'title' => 'Alamat', 'desc_id' => 'Jl. Merdeka', 'desc_en' => 'Merdeka Street']);

        $category = Category::create(['name_id' => 'Berita', 'name_en' => 'News', 'slug' => 'berita']);
        Article::create([
            'user_id' => $this->admin->id, 'created_by' => $this->admin->id, 'category_id' => $category->id,
            'title_id' => 'Artikel', 'title_en' => 'Article EN', 'slug' => 'artikel', 'desc_id' => '<p>Isi</p>',
            'img' => 'back/blog/a.jpg', 'status' => Article::STATUS_PUBLISH, 'publish_date' => now()->subDay(),
        ]);
        Config::create(['name' => 'blogname', 'value' => 'rrcons']);
    }

    public function test_every_admin_page_renders_in_both_languages(): void
    {
        $this->seedCms();

        $pages = [
            '/dashboard', '/users',
            '/home/menu', '/home/poster', '/home/hero', '/home/about', '/home/service', '/home/advantage',
            '/home/profile', '/home/business', '/home/sosmed', '/home/gallery',
            '/rrfuture/menu', '/rrfuture/hero', '/rrfuture/product',
            '/service/menu', '/service/hero', '/service/ourService',
            '/contact/menu', '/contact/hero', '/contact/usContact',
            '/blog/articles', '/blog/articles/create', '/blog/articles/' . Article::first()->id,
            '/blog/articles/' . Article::first()->id . '/edit', '/blog/categories', '/blog/configs',
        ];

        foreach (['id', 'en'] as $locale) {
            foreach ($pages as $uri) {
                $this->actingAs($this->admin)->withSession(['app_locale' => $locale])->get($uri)->assertOk();
            }
        }

        $this->actingAs($this->admin)->withSession(['app_locale' => 'id'])->get('/home/about')
            ->assertSee('Bagian Tentang')->assertSee('Teks (English)');
        $this->actingAs($this->admin)->withSession(['app_locale' => 'en'])->get('/home/about')
            ->assertSee('About Section');
    }

    public function test_public_pages_show_cms_content_in_the_selected_language(): void
    {
        $this->seedCms();

        $this->withSession(['app_locale' => 'en'])->get('/rrfuture')->assertOk()->assertSee('Product One')->assertSee('Our Products');
        $this->withSession(['app_locale' => 'id'])->get('/rrfuture')->assertOk()->assertSee('Produk Satu');

        $this->withSession(['app_locale' => 'en'])->get('/service')->assertOk()->assertSee('Consulting');
        $this->withSession(['app_locale' => 'id'])->get('/service')->assertOk()->assertSee('Konsultasi');

        $this->withSession(['app_locale' => 'en'])->get('/contact')->assertOk()->assertSee('Merdeka Street')->assertSee('Phone');
        $this->withSession(['app_locale' => 'id'])->get('/contact')->assertOk()->assertSee('Jl. Merdeka')->assertSee('Telepon');

        $this->withSession(['app_locale' => 'en'])->get('/')->assertOk()->assertSee('Training')->assertSee('Event')->assertSee('About us')->assertSee('Service 1');
        $this->withSession(['app_locale' => 'id'])->get('/')->assertOk()->assertSee('Pelatihan')->assertSee('Acara')->assertSee('Tentang kami')->assertSee('Layanan 1');
    }

    public function test_english_falls_back_to_indonesian_when_empty(): void
    {
        $menu = RRfutureMenu::create(['title_id' => 'Produk', 'title_en' => null]);
        $product = ProductRRfuture::create(['rrfuture_menu_id' => $menu->id, 'name_id' => 'Hanya Indonesia', 'desc_id' => 'D']);

        app()->setLocale('en');
        $this->assertSame('Hanya Indonesia', $product->name);
        $this->assertSame('Produk', $menu->title);

        app()->setLocale('id');
        $this->assertSame('Hanya Indonesia', $product->name);
    }

    public function test_admin_saves_both_languages_for_cms_sections(): void
    {
        Storage::fake('public');
        $this->seedCms();

        $product = ProductRRfuture::first();
        $this->actingAs($this->admin)->post(route('rrfuture.product.update', $product->id), [
            'name_id' => 'Produk Baru', 'name_en' => 'New Product', 'desc_id' => 'Deskripsi', 'desc_en' => '',
        ])->assertRedirect();
        $this->assertDatabaseHas('product_rrfutures', ['id' => $product->id, 'name_en' => 'New Product', 'desc_en' => null]);

        $menu = ContactMenu::first();
        $this->actingAs($this->admin)->post(route('contact.menu.update', $menu->id), [
            'title_id' => 'Kontak', 'title_en' => 'Contact',
        ])->assertRedirect();
        $this->assertDatabaseHas('contact_menus', ['id' => $menu->id, 'title_id' => 'Kontak', 'title_en' => 'Contact']);

        $us = UsContact::first();
        $this->actingAs($this->admin)->post(route('contact.usContact.update', $us->id), [
            'title' => 'Diubah', 'desc_id' => 'Alamat baru', 'desc_en' => 'New address',
        ])->assertRedirect();
        $this->assertDatabaseHas('us_contacts', ['id' => $us->id, 'title' => 'Alamat', 'desc_en' => 'New address']);

        $this->actingAs($this->admin)->post(route('home.gallery.create'), [
            'title_id' => 'Galeri', 'title_en' => 'Gallery', 'month' => 2, 'year' => 2025,
        ])->assertSessionHasErrors('img');

        $this->actingAs($this->admin)->post(route('home.gallery.create'), [
            'title_id' => 'Galeri', 'title_en' => 'Gallery', 'month' => 2, 'year' => 2025,
            'img' => UploadedFile::fake()->image('g.jpg'),
        ])->assertRedirect();
        $this->assertDatabaseHas('gallery_homes', ['title_en' => 'Gallery', 'desc_id' => null]);

        $sosmed = SosmedHome::first();
        $this->actingAs($this->admin)->post(route('home.sosmed.update', $sosmed->id), [
            'title_id' => 'Video', 'title_en' => 'Video EN', 'sosmed_link' => '',
        ])->assertRedirect();
        $this->assertDatabaseHas('sosmed_homes', ['id' => $sosmed->id, 'sosmed_link' => null, 'title_en' => 'Video EN']);

        $this->actingAs($this->admin)->post(route('home.poster.create'), [
            'name_id' => 'Poster Baru', 'name_en' => 'New Poster', 'img' => UploadedFile::fake()->image('p.jpg'),
        ])->assertRedirect();
        $this->assertStringStartsWith('back/home/poster/', PosterHome::where('name_id', 'Poster Baru')->value('img'));

        $profile = ProfileHome::first();
        $this->actingAs($this->admin)->post(route('home.profile.update', $profile->id), [
            'name' => 'Rita Russo', 'position_id' => 'Pendiri', 'position_en' => 'Founder',
        ])->assertRedirect();
        $this->assertDatabaseHas('profile_homes', ['id' => $profile->id, 'position_en' => 'Founder']);
    }

    public function test_validation_messages_follow_the_selected_language(): void
    {
        $category = Category::create(['name_id' => 'Berita', 'slug' => 'berita']);

        $this->actingAs($this->admin)->withSession(['app_locale' => 'id'])
            ->put(route('blog.categories.update', $category->id), ['name_id' => ''])
            ->assertSessionHasErrors(['name_id' => 'Nama (Indonesia) wajib diisi.']);

        $this->actingAs($this->admin)->withSession(['app_locale' => 'en'])
            ->put(route('blog.categories.update', $category->id), ['name_id' => ''])
            ->assertSessionHasErrors(['name_id' => 'The name (Indonesian) field is required.']);
    }

    public function test_every_admin_interface_string_has_an_indonesian_translation(): void
    {
        $translations = json_decode(file_get_contents(lang_path('id.json')), true);
        $this->assertIsArray($translations);

        $files = [];
        foreach (['resources/views/back', 'app/Http/Controllers', 'app/Http/Requests'] as $dir) {
            $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(base_path($dir)));
            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $files[] = $file->getPathname();
                }
            }
        }

        $missing = [];
        foreach ($files as $file) {
            $source = str_replace("\\'", "\x01", file_get_contents($file));
            preg_match_all("/__\(\s*'([^']*)'/", $source, $matches);

            foreach ($matches[1] as $key) {
                $key = str_replace("\x01", "'", $key);
                if (preg_match('/^[a-z_]+(\.[a-z_]+)+$/', $key)) {
                    continue; // kunci file PHP, mis. blog.sections.blog...
                }
                if (!array_key_exists($key, $translations)) {
                    $missing[] = $key . '  (' . basename($file) . ')';
                }
            }
        }

        $this->assertSame([], array_values(array_unique($missing)), 'Missing translations in lang/id.json');
    }

    public function test_indonesian_and_english_lang_files_have_the_same_keys(): void
    {
        foreach (glob(lang_path('id/*.php')) as $idFile) {
            $enFile = lang_path('en/' . basename($idFile));

            if (basename($idFile) === 'validation.php') {
                continue; // English memakai pesan bawaan Laravel
            }

            $this->assertFileExists($enFile);
            $idKeys = array_keys(Arr::dot(require $idFile));
            $enKeys = array_keys(Arr::dot(require $enFile));

            $this->assertSame([], array_values(array_diff($idKeys, $enKeys)), basename($idFile) . ': keys missing in English');
            $this->assertSame([], array_values(array_diff($enKeys, $idKeys)), basename($idFile) . ': keys missing in Indonesian');
        }
    }
}

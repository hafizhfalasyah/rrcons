<?php

namespace Tests\Feature;

use App\Models\Blog\Article;
use App\Models\Blog\Category;
use App\Models\Home\AboutHome;
use App\Models\Home\HeroHome;
use App\Models\Home\HomeMenu;
use App\Models\Home\ServiceHome;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ContentManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    }

    private function homeMenu(): HomeMenu
    {
        return HomeMenu::create(['title_id' => 'Tentang', 'title_en' => 'About']);
    }

    private function article(Category $category, string $title, string $slug): Article
    {
        return Article::create([
            'user_id' => $this->admin->id,
            'created_by' => $this->admin->id,
            'category_id' => $category->id,
            'title' => $title,
            'slug' => $slug,
            'desc' => 'Body',
            'img' => 'back/blog/x.jpg',
            'status' => Article::STATUS_PUBLISH,
            'publish_date' => now()->subDay(),
        ]);
    }

    public function test_article_slugs_are_unique_and_avoid_admin_routes(): void
    {
        $category = Category::create(['name' => 'News', 'slug' => 'news']);
        $this->article($category, 'Hello', 'hello');

        $this->assertSame('hello-2', Article::uniqueSlug('Hello'));
        $this->assertSame('hello', Article::uniqueSlug('Hello', Article::first()->id));
        $this->assertSame('articles-2', Article::uniqueSlug('Articles'));
        $this->assertSame('article', Article::uniqueSlug('!!!'));
    }

    public function test_admin_store_generates_a_unique_slug(): void
    {
        Storage::fake('public');
        $category = Category::create(['name' => 'News', 'slug' => 'news']);
        $this->article($category, 'Same Title', 'same-title');

        $this->actingAs($this->admin)->post(route('blog.articles.store'), [
            'title' => 'Same Title',
            'category_id' => $category->id,
            'desc' => '<p>Body</p>',
            'img' => UploadedFile::fake()->image('cover.jpg'),
            'status' => Article::STATUS_PUBLISH,
            'publish_date' => now()->subMinute()->format('Y-m-d\TH:i'),
        ])->assertRedirect();

        $this->assertDatabaseHas('articles', ['slug' => 'same-title-2']);
        $this->get('/blog/same-title-2')->assertOk();
    }

    public function test_category_with_articles_cannot_be_deleted(): void
    {
        $category = Category::create(['name' => 'News', 'slug' => 'news']);
        $this->article($category, 'Hello', 'hello');
        $empty = Category::create(['name' => 'Empty', 'slug' => 'empty']);

        $this->actingAs($this->admin)
            ->delete(route('blog.categories.destroy', $category->id))
            ->assertSessionHasErrors('category');
        $this->assertDatabaseHas('categories', ['id' => $category->id]);

        $this->actingAs($this->admin)->delete(route('blog.categories.destroy', $empty->id))->assertRedirect();
        $this->assertDatabaseMissing('categories', ['id' => $empty->id]);
    }

    public function test_category_slugs_are_unique(): void
    {
        Category::create(['name' => 'News', 'slug' => 'news']);

        $this->actingAs($this->admin)->post(route('blog.categories.store'), ['name' => 'News'])->assertRedirect();

        $this->assertDatabaseHas('categories', ['slug' => 'news-2']);
    }

    public function test_hero_upload_stores_full_path_and_removes_the_legacy_file(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('back/home/hero/old.jpg', 'old');

        $hero = HeroHome::create(['home_menu_id' => $this->homeMenu()->id, 'bg_img' => 'old.jpg']);

        $this->actingAs($this->admin)->post(route('home.hero.update', $hero->id), [
            'bg_img' => UploadedFile::fake()->image('new.jpg'),
        ])->assertRedirect();

        $path = $hero->fresh()->bg_img;

        $this->assertStringStartsWith('back/home/hero/', $path);
        Storage::disk('public')->assertExists($path);
        Storage::disk('public')->assertMissing('back/home/hero/old.jpg');
    }

    public function test_admin_updates_both_languages_of_translated_sections(): void
    {
        $menu = $this->homeMenu();
        $about = AboutHome::create(['home_menu_id' => $menu->id, 'text_id' => 'Lama', 'text_en' => 'Old']);
        $service = ServiceHome::create(['home_menu_id' => $menu->id, 'name_id' => 'Lama', 'name_en' => 'Old', 'desc_id' => 'Lama']);

        $this->actingAs($this->admin)->post(route('home.menu.update', $menu->id), [
            'title_id' => 'Tentang Kami',
            'title_en' => 'About Us',
        ])->assertRedirect();

        $this->actingAs($this->admin)->post(route('home.about.update', $about->id), [
            'text_id' => 'Teks baru',
            'text_en' => 'New text',
        ])->assertRedirect();

        $this->actingAs($this->admin)->post(route('home.service.update', $service->id), [
            'name_id' => 'Layanan',
            'name_en' => 'Service',
            'desc_id' => 'Deskripsi',
            'desc_en' => 'Description',
        ])->assertRedirect();

        $this->assertDatabaseHas('home_menus', ['id' => $menu->id, 'title_id' => 'Tentang Kami', 'title_en' => 'About Us']);
        $this->assertDatabaseHas('about_homes', ['id' => $about->id, 'text_id' => 'Teks baru', 'text_en' => 'New text']);
        $this->assertDatabaseHas('service_homes', ['id' => $service->id, 'name_en' => 'Service', 'desc_id' => 'Deskripsi']);

        $this->actingAs($this->admin)->get(route('home.about'))->assertOk()->assertSee('name="text_en"', false);
    }

    public function test_legacy_file_names_are_normalized_by_the_migration(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('public/back/home/hero/misplaced.jpg', 'x');

        $menu = $this->homeMenu();
        DB::table('hero_homes')->insert([
            ['home_menu_id' => $menu->id, 'bg_img' => 'legacy.jpg'],
            ['home_menu_id' => $menu->id, 'bg_img' => 'misplaced.jpg'],
            ['home_menu_id' => $menu->id, 'bg_img' => 'back/home/hero/already.jpg'],
        ]);

        $migration = require database_path('migrations/2026_10_12_000000_normalize_uploaded_file_paths.php');
        $migration->up();
        $migration->up(); // idempotent

        $this->assertSame(
            ['back/home/hero/legacy.jpg', 'back/home/hero/misplaced.jpg', 'back/home/hero/already.jpg'],
            DB::table('hero_homes')->orderBy('id')->pluck('bg_img')->all()
        );
        Storage::disk('public')->assertExists('back/home/hero/misplaced.jpg');
    }
}

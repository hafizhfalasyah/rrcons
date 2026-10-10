<?php

namespace Tests\Feature;

use App\Models\Blog\Article;
use App\Models\Blog\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ArticleSchedulingTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->category = Category::create(['name_id' => 'Berita', 'name_en' => 'News', 'slug' => 'berita']);
    }

    private function makeArticle(string $title, string $status, $publishDate, array $extra = []): Article
    {
        return Article::create(array_merge([
            'user_id' => $this->user->id,
            'created_by' => $this->user->id,
            'category_id' => $this->category->id,
            'title_id' => $title,
            'slug' => str($title)->slug(),
            'desc_id' => '<p>' . $title . '</p>',
            'img' => 'back/blog/dummy.jpg',
            'status' => $status,
            'publish_date' => $publishDate,
        ], $extra));
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'title_id' => 'Artikel Baru',
            'title_en' => 'New Article',
            'category_id' => $this->category->id,
            'desc_id' => '<p>Isi</p>',
            'desc_en' => '<p>Body</p>',
            'img' => UploadedFile::fake()->image('cover.jpg'),
            'publication' => Article::CHOICE_PUBLISH,
        ], $overrides);
    }

    public function test_private_article_is_hidden_from_public_pages(): void
    {
        $this->makeArticle('Private Post', Article::STATUS_PRIVATE, now()->subDay());

        $this->get('/blog')->assertOk()->assertDontSee('Private Post');
        $this->get('/blog/private-post')->assertNotFound();
        $this->assertStringNotContainsString('Private Post', $this->getJson('/search-articles?q=Private')->json('html'));
        $this->assertStringNotContainsString('Private Post', $this->getJson('/get-articles')->json('html'));
        $this->post('/blog/private-post/comment', [
            'name' => 'Guest',
            'email' => 'guest@example.com',
            'content' => 'Hi',
        ])->assertNotFound();
    }

    public function test_scheduled_article_appears_only_after_publish_time(): void
    {
        $publishAt = now()->addDays(2)->setTime(9, 30);
        $article = $this->makeArticle('Scheduled Post', Article::STATUS_PUBLISH, $publishAt);

        $this->assertSame(Article::PUBLICATION_SCHEDULED, $article->publication_status);
        $this->assertSame(Article::CHOICE_SCHEDULE, $article->publication_choice);
        $this->get('/blog')->assertOk()->assertDontSee('Scheduled Post');
        $this->get('/blog/scheduled-post')->assertNotFound();

        $this->travelTo($publishAt->copy()->subMinute());
        $this->get('/blog/scheduled-post')->assertNotFound();

        $this->travelTo($publishAt);
        $this->assertSame(Article::PUBLICATION_PUBLISHED, $article->fresh()->publication_status);
        $this->get('/blog')->assertOk()->assertSee('Scheduled Post');
        $this->get('/blog/scheduled-post')->assertOk()->assertSee('Scheduled Post');
    }

    public function test_store_with_publish_now_is_visible_immediately(): void
    {
        Storage::fake('public');

        $this->actingAs($this->user)->post(route('blog.articles.store'), $this->payload())
            ->assertRedirect(route('blog.articles.index'))
            ->assertSessionHas('success', fn ($message) => str_contains($message, 'Published'));

        $article = Article::where('slug', 'artikel-baru')->firstOrFail();

        $this->assertSame(Article::PUBLICATION_PUBLISHED, $article->publication_status);
        $this->get('/blog/artikel-baru')->assertOk();
    }

    public function test_store_with_schedule_keeps_the_chosen_datetime(): void
    {
        Storage::fake('public');

        $this->actingAs($this->user)->post(route('blog.articles.store'), $this->payload([
            'publication' => Article::CHOICE_SCHEDULE,
            'publish_date' => '2099-01-15T08:45',
        ]))->assertRedirect(route('blog.articles.index'))
            ->assertSessionHas('success', fn ($message) => str_contains($message, 'Scheduled'));

        $article = Article::where('slug', 'artikel-baru')->firstOrFail();

        $this->assertSame('2099-01-15 08:45', $article->publish_date->format('Y-m-d H:i'));
        $this->assertSame(Article::PUBLICATION_SCHEDULED, $article->publication_status);
        $this->get('/blog/artikel-baru')->assertNotFound();
    }

    public function test_store_as_private_is_hidden(): void
    {
        Storage::fake('public');

        $this->actingAs($this->user)->post(route('blog.articles.store'), $this->payload([
            'publication' => Article::CHOICE_PRIVATE,
            'publish_date' => '2099-01-15T08:45', // diabaikan untuk Private
        ]))->assertRedirect(route('blog.articles.index'));

        $article = Article::where('slug', 'artikel-baru')->firstOrFail();

        $this->assertSame(Article::PUBLICATION_PRIVATE, $article->publication_status);
        $this->get('/blog/artikel-baru')->assertNotFound();
    }

    public function test_schedule_requires_a_future_datetime(): void
    {
        Storage::fake('public');

        $this->actingAs($this->user)->post(route('blog.articles.store'), $this->payload([
            'publication' => Article::CHOICE_SCHEDULE,
        ]))->assertSessionHasErrors('publish_date');

        $this->actingAs($this->user)->post(route('blog.articles.store'), $this->payload([
            'publication' => Article::CHOICE_SCHEDULE,
            'publish_date' => now()->subHour()->format('Y-m-d\TH:i'),
        ]))->assertSessionHasErrors('publish_date');

        $this->assertDatabaseCount('articles', 0);
    }

    public function test_editing_a_published_article_keeps_its_publish_date(): void
    {
        $original = now()->subDays(10)->startOfMinute();
        $article = $this->makeArticle('Old Post', Article::STATUS_PUBLISH, $original);

        $this->actingAs($this->user)->put(route('blog.articles.update', $article->id), $this->payload([
            'title_id' => 'Old Post',
            'img' => null,
            'publication' => Article::CHOICE_PUBLISH,
        ]))->assertRedirect(route('blog.articles.index'));

        $this->assertTrue($article->fresh()->publish_date->equalTo($original));
    }

    public function test_publishing_a_private_article_uses_the_current_time(): void
    {
        $article = $this->makeArticle('Draft Post', Article::STATUS_PRIVATE, now()->subYear());

        $this->actingAs($this->user)->put(route('blog.articles.update', $article->id), $this->payload([
            'title_id' => 'Draft Post',
            'img' => null,
            'publication' => Article::CHOICE_PUBLISH,
        ]))->assertRedirect();

        $article->refresh();
        $this->assertSame(Article::PUBLICATION_PUBLISHED, $article->publication_status);
        $this->assertTrue($article->publish_date->greaterThan(now()->subMinute()));
    }

    public function test_admin_rejects_invalid_publication_choice(): void
    {
        $article = $this->makeArticle('Bad Status', Article::STATUS_PUBLISH, now()->subDay());

        $this->actingAs($this->user)->put(route('blog.articles.update', $article->id), $this->payload([
            'img' => null,
            'publication' => 'draft',
        ]))->assertSessionHasErrors('publication');
    }

    public function test_admin_forms_explain_the_publication_choices(): void
    {
        $article = $this->makeArticle('Form Post', Article::STATUS_PUBLISH, '2099-05-20 14:15:00');

        $this->actingAs($this->user)->get(route('blog.articles.create'))
            ->assertOk()
            ->assertSee('name="publication"', false)
            ->assertSee('Publish now')
            ->assertSee('Schedule')
            ->assertSee('Private');

        $this->actingAs($this->user)->get(route('blog.articles.edit', $article->id))
            ->assertOk()
            ->assertSee('value="2099-05-20T14:15"', false)
            ->assertSee('id="publication-schedule" value="schedule" autocomplete="off" checked', false);

        $this->actingAs($this->user)->get(route('blog.articles.show', $article->id))
            ->assertOk()
            ->assertSee('Scheduled')
            ->assertSee('will be published automatically');

        $this->actingAs($this->user)->getJson(route('blog.articles.index'), ['X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk()
            ->assertJsonPath('data.0.title', fn ($title) => str_contains($title, 'Form Post'));
    }

    public function test_admin_interface_follows_the_selected_language(): void
    {
        $this->actingAs($this->user)->withSession(['app_locale' => 'id'])
            ->get(route('blog.articles.create'))
            ->assertOk()
            ->assertSee('Terbitkan sekarang')
            ->assertSee('Jadwalkan')
            ->assertSee('Buat Artikel');

        $this->actingAs($this->user)->withSession(['app_locale' => 'en'])
            ->get(route('blog.articles.create'))
            ->assertOk()
            ->assertSee('Publish now')
            ->assertSee('Create Article');
    }

    public function test_article_content_follows_the_selected_language(): void
    {
        $this->makeArticle('Judul Indonesia', Article::STATUS_PUBLISH, now()->subDay(), [
            'title_en' => 'English Title',
            'desc_en' => '<p>English body</p>',
            'slug' => 'judul-indonesia',
        ]);
        $this->makeArticle('Hanya Indonesia', Article::STATUS_PUBLISH, now()->subDay(), ['slug' => 'hanya-indonesia']);

        $this->withSession(['app_locale' => 'id'])->get('/blog/judul-indonesia')
            ->assertOk()->assertSee('Judul Indonesia')->assertDontSee('English Title');

        $this->withSession(['app_locale' => 'en'])->get('/blog/judul-indonesia')
            ->assertOk()->assertSee('English Title')->assertSee('English body');

        // fallback ke Bahasa Indonesia bila versi English kosong
        $this->withSession(['app_locale' => 'en'])->get('/blog/hanya-indonesia')
            ->assertOk()->assertSee('Hanya Indonesia');

        // kategori juga ikut bahasa
        $this->withSession(['app_locale' => 'en'])->get('/blog')->assertSee('News');

        // pencarian mencakup kedua bahasa
        $this->assertStringContainsString(
            'English Title',
            $this->withSession(['app_locale' => 'en'])->getJson('/search-articles?q=English')->json('html')
        );
    }

    public function test_article_html_is_sanitized(): void
    {
        Storage::fake('public');

        $this->actingAs($this->user)->post(route('blog.articles.store'), $this->payload([
            'desc_id' => '<p>Aman</p><script>alert("xss")</script><img src="x" onerror="alert(1)"><a href="javascript:alert(1)">klik</a>',
        ]))->assertRedirect();

        $article = Article::where('slug', 'artikel-baru')->firstOrFail();

        $this->assertStringNotContainsString('<script', $article->desc_id);
        $this->assertStringNotContainsString('onerror', $article->desc_id);
        $this->assertStringNotContainsString('javascript:', $article->desc_id);
        $this->assertStringContainsString('<p>Aman</p>', $article->desc_id);

        // konten lama yang tersimpan sebelum sanitasi tetap dibersihkan saat ditampilkan
        $article->forceFill(['desc_id' => '<p>Lama</p><script>alert(2)</script>'])->save();
        $this->withSession(['app_locale' => 'id'])->get('/blog/artikel-baru')
            ->assertOk()->assertSee('Lama')->assertDontSee('<script>alert(2)</script>', false);
    }
}

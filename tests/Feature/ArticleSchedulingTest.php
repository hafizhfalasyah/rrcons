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

    /**
     * Migrasi translate (2025_07_*) belum bisa dijalankan dari database kosong,
     * jadi test ini hanya memigrasikan tabel yang dibutuhkan fitur blog.
     */
    protected function migrateFreshUsing()
    {
        return [
            '--drop-views' => $this->shouldDropViews(),
            '--drop-types' => $this->shouldDropTypes(),
            '--path' => [
                'database/migrations/2014_10_12_000000_create_users_table.php',
                'database/migrations/2024_10_16_141319_create_categories_table.php',
                'database/migrations/2024_10_16_141320_create_articles_table.php',
                'database/migrations/2025_06_27_070846_create_visitors_table.php',
                'database/migrations/2025_07_06_181334_create_comments_table.php',
                'database/migrations/2026_10_11_000000_change_publish_date_to_datetime_on_articles_table.php',
            ],
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->category = Category::create(['name' => 'News', 'slug' => 'news']);
    }

    private function makeArticle(string $title, string $status, $publishDate): Article
    {
        return Article::create([
            'user_id' => $this->user->id,
            'created_by' => $this->user->id,
            'category_id' => $this->category->id,
            'title' => $title,
            'slug' => str($title)->slug(),
            'desc' => '<p>' . $title . '</p>',
            'img' => 'back/blog/dummy.jpg',
            'status' => $status,
            'publish_date' => $publishDate,
        ]);
    }

    public function test_private_article_is_hidden_from_public_pages(): void
    {
        $this->makeArticle('Private Post', Article::STATUS_PRIVATE, now()->subDay());

        $this->get('/blog')->assertOk()->assertDontSee('Private Post');
        $this->get('/blog/private-post')->assertNotFound();
        $this->getJson('/search-articles?q=Private')->assertOk()->assertJsonMissing(['html' => 'Private Post']);
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
        $this->get('/blog')->assertOk()->assertDontSee('Scheduled Post');
        $this->get('/blog/scheduled-post')->assertNotFound();

        $this->travelTo($publishAt->copy()->subMinute());
        $this->get('/blog/scheduled-post')->assertNotFound();

        $this->travelTo($publishAt);
        $this->assertSame(Article::PUBLICATION_PUBLISHED, $article->fresh()->publication_status);
        $this->get('/blog')->assertOk()->assertSee('Scheduled Post');
        $this->get('/blog/scheduled-post')->assertOk()->assertSee('Scheduled Post');
    }

    public function test_published_article_with_past_date_is_visible(): void
    {
        $this->makeArticle('Live Post', Article::STATUS_PUBLISH, now()->subHour());

        $this->get('/blog')->assertOk()->assertSee('Live Post');
        $this->get('/blog/live-post')->assertOk();
    }

    public function test_admin_store_keeps_the_chosen_publish_date(): void
    {
        Storage::fake('public');

        $this->actingAs($this->user)->post(route('blog.articles.store'), [
            'title' => 'Future Post',
            'category_id' => $this->category->id,
            'desc' => '<p>Body</p>',
            'img' => UploadedFile::fake()->image('cover.jpg'),
            'status' => Article::STATUS_PUBLISH,
            'publish_date' => '2099-01-15T08:45',
        ])->assertRedirect(route('blog.articles.index'));

        $article = Article::where('slug', 'future-post')->firstOrFail();

        $this->assertSame('2099-01-15 08:45', $article->publish_date->format('Y-m-d H:i'));
        $this->assertSame(Article::PUBLICATION_SCHEDULED, $article->publication_status);
        $this->get('/blog/future-post')->assertNotFound();
    }

    public function test_admin_update_keeps_the_chosen_publish_date_and_status(): void
    {
        $article = $this->makeArticle('Edit Post', Article::STATUS_PUBLISH, now()->subDay());

        $this->actingAs($this->user)->put(route('blog.articles.update', $article->id), [
            'title' => 'Edit Post',
            'category_id' => $this->category->id,
            'desc' => '<p>Body</p>',
            'status' => Article::STATUS_PRIVATE,
            'publish_date' => '2099-03-01T10:00',
        ])->assertRedirect(route('blog.articles.index'));

        $article->refresh();

        $this->assertSame('2099-03-01 10:00', $article->publish_date->format('Y-m-d H:i'));
        $this->assertSame(Article::PUBLICATION_PRIVATE, $article->publication_status);
        $this->get('/blog/edit-post')->assertNotFound();
    }

    public function test_admin_forms_render_the_publish_datetime(): void
    {
        $article = $this->makeArticle('Form Post', Article::STATUS_PUBLISH, '2099-05-20 14:15:00');

        $this->actingAs($this->user)->get(route('blog.articles.create'))
            ->assertOk()
            ->assertSee('type="datetime-local"', false);

        $this->actingAs($this->user)->get(route('blog.articles.edit', $article->id))
            ->assertOk()
            ->assertSee('value="2099-05-20T14:15"', false);

        $this->actingAs($this->user)->get(route('blog.articles.show', $article->id))
            ->assertOk()
            ->assertSee('Scheduled');
    }

    public function test_admin_rejects_invalid_status(): void
    {
        $article = $this->makeArticle('Bad Status', Article::STATUS_PUBLISH, now()->subDay());

        $this->actingAs($this->user)->put(route('blog.articles.update', $article->id), [
            'title' => 'Bad Status',
            'category_id' => $this->category->id,
            'desc' => '<p>Body</p>',
            'status' => 'draft',
            'publish_date' => 'not-a-date',
        ])->assertSessionHasErrors(['status', 'publish_date']);
    }
}

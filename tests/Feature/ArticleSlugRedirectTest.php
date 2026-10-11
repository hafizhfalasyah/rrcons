<?php

namespace Tests\Feature;

use App\Models\Blog\Article;
use App\Models\Blog\ArticleSlugRedirect;
use App\Models\Blog\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleSlugRedirectTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->category = Category::create(['name_id' => 'Berita', 'slug' => 'berita']);
    }

    private function article(string $title, array $attributes = []): Article
    {
        return Article::create($attributes + [
            'user_id' => $this->admin->id, 'created_by' => $this->admin->id, 'category_id' => $this->category->id,
            'title_id' => $title, 'slug' => Article::uniqueSlug($title), 'desc_id' => '<p>Isi</p>', 'img' => 'back/blog/a.jpg',
            'status' => Article::STATUS_PUBLISH, 'publish_date' => now()->subDay(),
        ]);
    }

    private function renameThroughAdmin(Article $article, string $title)
    {
        return $this->actingAs($this->admin)->put("/blog/articles/{$article->id}", [
            'category_id' => $this->category->id,
            'title_id' => $title,
            'desc_id' => '<p>Isi</p>',
            'publication' => Article::CHOICE_PUBLISH,
        ]);
    }

    public function test_renaming_an_article_redirects_the_old_url_permanently(): void
    {
        $article = $this->article('Judul Lama');

        $this->renameThroughAdmin($article, 'Judul Baru')
            ->assertRedirect(route('blog.articles.index'))
            ->assertSessionHas('success', fn ($message) => str_contains($message, '/blog/judul-baru') && str_contains($message, '/blog/judul-lama'));

        $this->assertSame('judul-baru', $article->fresh()->slug);

        $this->get('/blog/judul-lama?lang=en')->assertStatus(301)->assertRedirect(route('detail_blog', 'judul-baru') . '?lang=en');
        $this->get('/blog/judul-baru')->assertOk()->assertSee('Judul Baru');
    }

    public function test_every_previous_slug_points_straight_to_the_latest_url(): void
    {
        $article = $this->article('Versi Satu');

        $this->renameThroughAdmin($article, 'Versi Dua');
        $this->renameThroughAdmin($article->fresh(), 'Versi Tiga');

        $this->get('/blog/versi-satu')->assertStatus(301)->assertRedirect(route('detail_blog', 'versi-tiga'));
        $this->get('/blog/versi-dua')->assertStatus(301)->assertRedirect(route('detail_blog', 'versi-tiga'));
    }

    public function test_switching_back_to_an_old_title_reuses_its_url_without_a_redirect_loop(): void
    {
        $article = $this->article('Asli');

        $this->renameThroughAdmin($article, 'Sementara');
        $this->renameThroughAdmin($article->fresh(), 'Asli');

        $this->assertSame('asli', $article->fresh()->slug);
        $this->assertFalse(ArticleSlugRedirect::where('slug', 'asli')->exists());
        $this->get('/blog/asli')->assertOk();
        $this->get('/blog/sementara')->assertStatus(301)->assertRedirect(route('detail_blog', 'asli'));
    }

    public function test_an_old_url_is_never_taken_over_by_another_article(): void
    {
        $first = $this->article('Topik Hangat');
        $this->renameThroughAdmin($first, 'Topik Hangat Diperbarui');

        $second = $this->article('Topik Hangat');

        $this->assertSame('topik-hangat-2', $second->slug);
        $this->get('/blog/topik-hangat')->assertStatus(301)->assertRedirect(route('detail_blog', 'topik-hangat-diperbarui'));
    }

    public function test_old_urls_of_hidden_or_deleted_articles_return_not_found(): void
    {
        $private = $this->article('Rahasia');
        $this->renameThroughAdmin($private, 'Rahasia Baru');
        $private->fresh()->update(['status' => Article::STATUS_PRIVATE]);

        $this->get('/blog/rahasia')->assertNotFound();

        $deleted = $this->article('Akan Dihapus');
        $this->renameThroughAdmin($deleted, 'Sudah Diganti');
        $this->actingAs($this->admin)->deleteJson("/blog/articles/{$deleted->id}")->assertOk();
        auth()->logout();

        $this->assertFalse(ArticleSlugRedirect::where('slug', 'akan-dihapus')->exists());
        $this->get('/blog/akan-dihapus')->assertNotFound();
    }

    public function test_admin_article_page_lists_the_old_urls(): void
    {
        $article = $this->article('Judul Awal');
        $this->renameThroughAdmin($article, 'Judul Akhir');

        $this->actingAs($this->admin)->get("/blog/articles/{$article->id}")
            ->assertOk()
            ->assertSee('/blog/judul-akhir')
            ->assertSee('/blog/judul-awal');
    }

    public function test_comments_is_a_reserved_article_slug(): void
    {
        $this->assertSame('comments-2', Article::uniqueSlug('Comments'));
    }
}

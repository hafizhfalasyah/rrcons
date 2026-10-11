<?php

namespace Tests\Feature;

use App\Models\Blog\Article;
use App\Models\Blog\Category;
use App\Models\Blog\Comment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CommentModerationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Article $article;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => User::ROLE_ADMIN, 'name' => 'Admin Moderator']);
        $category = Category::create(['name_id' => 'Berita', 'slug' => 'berita']);
        $this->article = Article::create([
            'user_id' => $this->admin->id, 'created_by' => $this->admin->id, 'category_id' => $category->id,
            'title_id' => 'Artikel Diskusi', 'slug' => 'artikel-diskusi', 'desc_id' => '<p>Isi</p>', 'img' => 'back/blog/a.jpg',
            'status' => Article::STATUS_PUBLISH, 'publish_date' => now()->subDay(),
        ]);
    }

    private function submit(array $data = [])
    {
        return $this->from('/blog/artikel-diskusi')->post('/blog/artikel-diskusi/comment', $data + [
            'name' => 'Budi', 'email' => 'budi@example.com', 'content' => 'Komentar menunggu',
        ]);
    }

    public function test_new_comments_wait_for_approval_and_are_hidden(): void
    {
        $this->withSession(['app_locale' => 'id']);
        $this->submit()->assertRedirect('/blog/artikel-diskusi')
            ->assertSessionHas('success', 'Terima kasih! Komentar Anda akan tampil setelah disetujui admin.');

        $comment = Comment::firstOrFail();
        $this->assertSame(Comment::STATUS_PENDING, $comment->status);

        $this->get('/blog/artikel-diskusi')->assertOk()->assertDontSee('Komentar menunggu')->assertSee('Komentar (0)');
    }

    public function test_visitors_cannot_choose_the_status_themselves(): void
    {
        $this->submit(['status' => Comment::STATUS_APPROVED]);

        $this->assertSame(Comment::STATUS_PENDING, Comment::firstOrFail()->status);
    }

    public function test_approved_comments_are_shown_and_spam_is_hidden(): void
    {
        $this->submit(['content' => 'Komentar bagus']);
        $this->submit(['content' => 'Beli obat murah']);
        [$good, $spam] = Comment::orderBy('id')->get()->all();

        $this->actingAs($this->admin)->put("/blog/comments/{$good->id}", ['status' => Comment::STATUS_APPROVED])->assertRedirect();
        $this->actingAs($this->admin)->put("/blog/comments/{$spam->id}", ['status' => Comment::STATUS_SPAM])->assertRedirect();

        $good->refresh();
        $this->assertSame($this->admin->id, $good->moderated_by);
        $this->assertNotNull($good->moderated_at);

        auth()->logout();
        $this->withSession(['app_locale' => 'id'])->get('/blog/artikel-diskusi')
            ->assertSee('Komentar bagus')
            ->assertDontSee('Beli obat murah')
            ->assertSee('Komentar (1)');
    }

    public function test_moderation_page_filters_by_status_and_searches(): void
    {
        $this->submit(['name' => 'Penunggu', 'content' => 'Masih menunggu']);
        $this->submit(['name' => 'Disetujui', 'content' => 'Sudah tampil']);
        Comment::where('name', 'Disetujui')->firstOrFail()->moderate(Comment::STATUS_APPROVED, $this->admin);

        $this->actingAs($this->admin)->get('/blog/comments')
            ->assertOk()->assertSee('Masih menunggu')->assertDontSee('Sudah tampil');

        $this->actingAs($this->admin)->get('/blog/comments?status=approved')
            ->assertOk()->assertSee('Sudah tampil')->assertDontSee('Masih menunggu');

        $this->actingAs($this->admin)->get('/blog/comments?status=all&q=Penunggu')
            ->assertOk()->assertSee('Masih menunggu')->assertDontSee('Sudah tampil');

        // badge jumlah komentar menunggu di sidebar
        $this->actingAs($this->admin)->get('/dashboard')->assertSee('title="Waiting for approval">1</span>', false);
    }

    public function test_comments_can_be_moved_back_to_pending_and_deleted(): void
    {
        $this->submit();
        $comment = Comment::firstOrFail();
        $comment->moderate(Comment::STATUS_APPROVED, $this->admin);

        $this->actingAs($this->admin)->put("/blog/comments/{$comment->id}", ['status' => Comment::STATUS_PENDING]);
        $this->assertSame(Comment::STATUS_PENDING, $comment->fresh()->status);

        $this->actingAs($this->admin)->delete("/blog/comments/{$comment->id}")->assertRedirect();
        $this->assertSame(0, Comment::count());
    }

    public function test_invalid_status_is_rejected(): void
    {
        $this->submit();
        $comment = Comment::firstOrFail();

        $this->actingAs($this->admin)->put("/blog/comments/{$comment->id}", ['status' => 'published'])->assertSessionHasErrors('status');
        $this->assertSame(Comment::STATUS_PENDING, $comment->fresh()->status);
    }

    public function test_writers_can_moderate_but_uploaders_cannot(): void
    {
        $this->submit();
        $comment = Comment::firstOrFail();

        $writer = User::factory()->create(['role' => User::ROLE_WRITER]);
        $this->actingAs($writer)->get('/blog/comments')->assertOk();
        $this->actingAs($writer)->put("/blog/comments/{$comment->id}", ['status' => Comment::STATUS_APPROVED])->assertRedirect();

        $uploader = User::factory()->create(['role' => User::ROLE_UPLOADER]);
        $this->actingAs($uploader)->get('/blog/comments')->assertForbidden();
        $this->actingAs($uploader)->delete("/blog/comments/{$comment->id}")->assertForbidden();
        $this->assertSame(1, Comment::count());
    }

    public function test_comments_written_before_moderation_existed_stay_visible(): void
    {
        $migration = require database_path('migrations/2026_10_17_000100_add_moderation_to_comments_table.php');
        $migration->down();
        $this->assertFalse(Schema::hasColumn('comments', 'status'));

        DB::table('comments')->insert([
            'article_id' => $this->article->id, 'name' => 'Lama', 'email' => 'lama@example.com',
            'content' => 'Komentar dari dulu', 'created_at' => now(), 'updated_at' => now(),
        ]);

        $migration->up();
        $migration->up(); // idempoten

        $this->assertSame(Comment::STATUS_APPROVED, Comment::firstOrFail()->status);
        $this->get('/blog/artikel-diskusi')->assertSee('Komentar dari dulu');

        // komentar baru setelah migration tetap menunggu persetujuan
        $this->submit();
        $this->assertSame(Comment::STATUS_PENDING, Comment::latest('id')->firstOrFail()->status);
    }

    public function test_moderation_page_renders_in_both_languages(): void
    {
        $this->submit();

        $this->actingAs($this->admin)->withSession(['app_locale' => 'id'])->get('/blog/comments')
            ->assertOk()->assertSee('Moderasi Komentar')->assertSee('Setujui');
        $this->actingAs($this->admin)->withSession(['app_locale' => 'en'])->get('/blog/comments')
            ->assertOk()->assertSee('Comment Moderation')->assertSee('Approve');
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageLoaderTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_standalone_page_template_includes_the_loading_screen(): void
    {
        $templates = [
            'front/index', 'front/rrfuture', 'front/service', 'front/blog', 'front/detail-blog', 'front/contact',
            'back/layout/template', 'auth/login',
        ];

        foreach ($templates as $template) {
            $source = file_get_contents(resource_path("views/{$template}.blade.php"));

            $this->assertSame(1, substr_count($source, "@include('partials.page-loader')"), "{$template} must include the loader once");
            $this->assertMatchesRegularExpression(
                "/<body[^>]*>\s*@include\('partials\.page-loader'\)/",
                $source,
                "{$template} must include the loader right after <body>"
            );
        }
    }

    public function test_public_and_login_pages_render_the_loader_in_the_active_language(): void
    {
        foreach (['/', '/login'] as $uri) {
            $this->get($uri . '?lang=id')->assertOk()
                ->assertSee('id="rr-loader"', false)
                ->assertSee('role="progressbar"', false)
                ->assertSee('Memuat halaman RRCONS')
                ->assertSee('Klik lingkaran untuk melewati');

            $this->get($uri . '?lang=en')->assertOk()
                ->assertSee('id="rr-loader"', false)
                ->assertSee('Loading RRCONS page')
                ->assertSee('Click the circle to skip');
        }
    }

    public function test_admin_panel_renders_the_loader_once(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $html = $this->actingAs($admin)->withSession(['app_locale' => 'id'])->get('/dashboard')->assertOk()->getContent();

        $this->assertSame(1, substr_count($html, 'id="rr-loader"'));
        $this->assertStringContainsString('Memuat gambar :loaded\/:total', $html);
    }

    public function test_loader_uses_the_white_and_blue_site_theme_without_a_logo(): void
    {
        $source = file_get_contents(resource_path('views/partials/page-loader.blade.php'));

        $this->assertStringContainsString('background: #fff;', $source);
        $this->assertStringContainsString('--rr-blue: #2563eb;', $source);
        $this->assertStringNotContainsString('<img', $source);
        $this->assertStringNotContainsString('logo', strtolower($source));
    }

    public function test_loader_hides_itself_without_javascript(): void
    {
        $this->get('/')->assertOk()
            ->assertSee('<noscript><style>#rr-loader { display: none !important; }', false);
    }
}

<?php

namespace Tests\Feature;

use App\Models\Home\PosterHome;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PosterSliderTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_poster_is_rendered_as_a_stacked_slide_and_only_the_first_is_visible(): void
    {
        // poster hanya tampil bila file-nya ada di disk public
        Storage::fake('public');
        foreach (['a.jpg', 'b.jpg'] as $file) {
            Storage::disk('public')->putFileAs('back/home/poster', UploadedFile::fake()->image($file), $file);
        }

        PosterHome::create(['img' => 'back/home/poster/a.jpg', 'name_id' => 'Poster Satu', 'name_en' => 'Poster One']);
        PosterHome::create(['img' => 'back/home/poster/b.jpg', 'name_id' => 'Poster Dua', 'name_en' => 'Poster Two']);

        $html = $this->get('/?lang=id')->assertOk()->getContent();

        $this->assertSame(2, substr_count($html, 'data-poster-slide'));
        $this->assertStringContainsString('storage/back/home/poster/a.jpg', $html);
        $this->assertStringContainsString('storage/back/home/poster/b.jpg', $html);
        $this->assertStringContainsString('alt="Poster Satu"', $html);
        $this->assertStringContainsString('alt="Poster Dua"', $html);
        // poster selain yang pertama tersembunyi sejak HTML awal (sebelum Alpine berjalan)
        $this->assertSame(1, preg_match_all('/data-poster-slide\s+style="opacity: 0"/', $html));
        $this->assertStringNotContainsString('id="image-source"', $html);

        $this->get('/?lang=en')->assertOk()->assertSee('alt="Poster Two"', false);
    }

    public function test_poster_slider_animates_changes_and_respects_reduced_motion(): void
    {
        $script = file_get_contents(public_path('front/js/index.js'));

        $this->assertStringContainsString('animateSlide(from, this.currentIndex, 1)', $script);
        $this->assertStringContainsString('animateSlide(from, this.currentIndex, -1)', $script);
        $this->assertStringContainsString('prefers-reduced-motion: reduce', $script);
        $this->assertStringContainsString('[data-poster-slide]', $script);
    }
}

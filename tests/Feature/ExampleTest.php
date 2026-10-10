<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Halaman publik harus tetap bisa dirender walau konten CMS masih kosong
     * (mis. instalasi baru lewat Docker).
     */
    public function test_public_pages_render_on_an_empty_database(): void
    {
        foreach (['/', '/rrfuture', '/service', '/blog', '/contact', '/login'] as $uri) {
            $this->get($uri)->assertOk();
        }
    }
}

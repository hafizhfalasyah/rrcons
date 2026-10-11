<?php

namespace Tests\Feature;

use App\Models\Home\AboutHome;
use App\Models\Home\HomeMenu;
use App\Models\Home\ServiceHome;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminInterfaceTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $role): User
    {
        return User::factory()->create(['role' => $role, 'name' => ucfirst($role) . ' User']);
    }

    public function test_profile_menu_has_no_my_profile_link_and_a_red_logout(): void
    {
        $response = $this->actingAs($this->user(User::ROLE_ADMIN))->withSession(['app_locale' => 'en'])->get('/dashboard')->assertOk();

        $response->assertDontSee('My Profile')
            ->assertSee('class="dropdown-item text-danger fw-semibold d-flex align-items-center gap-2"', false)
            // tombol logout di bagian bawah sidebar yang selalu terlihat
            ->assertSee('class="sidebar-footer"', false)
            ->assertSee('class="btn btn-logout', false);
    }

    public function test_sidebar_only_lists_the_menus_each_role_may_open(): void
    {
        $this->actingAs($this->user(User::ROLE_ADMIN))->withSession(['app_locale' => 'en'])->get('/dashboard')
            ->assertSee('Website content')->assertSee('Section Headings')->assertSee('Main Banner')
            ->assertSee('Blog Settings')->assertSee('Users');

        $this->actingAs($this->user(User::ROLE_WRITER))->withSession(['app_locale' => 'en'])->get('/dashboard')
            ->assertDontSee('Website content')->assertSee('Articles')->assertSee('Comments')
            ->assertDontSee('Blog Settings')->assertSee('My Account');

        $this->actingAs($this->user(User::ROLE_UPLOADER))->withSession(['app_locale' => 'en'])->get('/dashboard')
            ->assertSee('Pop-up Poster')->assertSee('Event Gallery')
            ->assertDontSee('Main Banner')->assertDontSee('href="' . route('blog.articles.index') . '"', false)->assertSee('My Account');
    }

    public function test_section_headings_page_explains_which_section_uses_each_heading(): void
    {
        $about = HomeMenu::create(['title_id' => 'Tentang Kami', 'sub_title_id' => 'Sub']);
        AboutHome::create(['home_menu_id' => $about->id, 'text_id' => 'Isi']);
        HomeMenu::create(['title_id' => 'Judul Lepas']);

        $this->actingAs($this->user(User::ROLE_ADMIN))->withSession(['app_locale' => 'id'])->get('/home/menu')
            ->assertOk()
            ->assertSee('Dipakai untuk')
            ->assertSee('href="' . route('home.about') . '"', false)
            ->assertSee('Belum dipakai');
    }

    public function test_a_failed_form_reopens_and_keeps_other_forms_untouched(): void
    {
        $admin = $this->user(User::ROLE_ADMIN);
        $menu = HomeMenu::create(['title_id' => 'Layanan']);
        $first = ServiceHome::create(['home_menu_id' => $menu->id, 'name_id' => 'Konsultasi', 'desc_id' => 'Deskripsi satu']);
        $second = ServiceHome::create(['home_menu_id' => $menu->id, 'name_id' => 'Pelatihan', 'desc_id' => 'Deskripsi dua']);

        $this->actingAs($admin)->from('/home/service')->post("/home/service/{$first->id}/update", [
            '_modal' => "edit-home-service-{$first->id}",
            'name_id' => 'Nama baru',
            'desc_id' => '',
        ])->assertRedirect('/home/service')->assertSessionHasErrors('desc_id');

        $page = $this->actingAs($admin)->get('/home/service')->assertOk();

        // modal yang gagal dibuka lagi otomatis dengan isian terakhir
        $page->assertSee('document.getElementById("edit-home-service-' . $first->id . '")', false)
            ->assertSee('value="Nama baru"', false);
        // form lain tetap menampilkan datanya sendiri
        $page->assertSee('value="Pelatihan"', false)->assertSee('Deskripsi dua');
        $this->assertSame('Konsultasi', $first->fresh()->name_id);
    }

    public function test_dashboard_is_organised_for_every_role(): void
    {
        foreach ([User::ROLE_ADMIN, User::ROLE_WRITER, User::ROLE_UPLOADER] as $role) {
            $response = $this->actingAs($this->user($role))->withSession(['app_locale' => 'id'])->get('/dashboard')->assertOk();
            $response->assertSee('Ringkasan konten')->assertSee('Pengunjung website')
                ->assertSee('id="realtime-active"', false)->assertSee('id="trend-chart"', false);
        }

        $this->actingAs($this->user(User::ROLE_UPLOADER))->withSession(['app_locale' => 'id'])->get('/dashboard')
            ->assertSee('Foto galeri')->assertSee('Poster aktif')->assertDontSee('Tulis artikel');
        $this->actingAs($this->user(User::ROLE_WRITER))->withSession(['app_locale' => 'id'])->get('/dashboard')
            ->assertSee('Artikel tayang')->assertSee('Tulis artikel')->assertDontSee('Foto galeri');
    }
}

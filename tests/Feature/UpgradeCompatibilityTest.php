<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Penjaga hasil upgrade Laravel 10 -> 13: tampilan & perilaku harus tetap sama seperti sebelumnya.
 */
class UpgradeCompatibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_error_pages_keep_the_previous_layout(): void
    {
        $this->get('/halaman-tidak-ada')
            ->assertNotFound()
            ->assertSee('<body class="antialiased">', false)
            ->assertSee('bg-gray-100 dark:bg-gray-900', false)
            ->assertDontSee('role="main"', false);
    }

    public function test_file_manager_uses_the_previous_interface(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)->get('/laravel-filemanager?type=Images')
            ->assertOk()
            ->assertSee('id="multi_selection_toggle"', false)
            ->assertDontSee('id="keyword"', false)
            ->assertSee(file_get_contents(public_path('vendor/laravel-filemanager/css/lfm.css')), false);
    }

    public function test_login_does_not_rehash_the_stored_password(): void
    {
        $hash = Hash::make('password123', ['rounds' => 5]);
        $user = User::factory()->create(['email' => 'rehash@example.com', 'password' => $hash, 'role' => User::ROLE_WRITER]);

        $this->post('/login', ['email' => 'rehash@example.com', 'password' => 'password123'])->assertRedirect('/dashboard');

        $this->assertSame($hash, $user->fresh()->password);
    }
}

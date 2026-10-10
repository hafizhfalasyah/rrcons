<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccessControlTest extends TestCase
{
    use RefreshDatabase;

    private function userWithRole(?string $role): User
    {
        return User::factory()->create(['role' => $role]);
    }

    public function test_public_registration_and_password_reset_are_disabled(): void
    {
        $this->get('/register')->assertNotFound();
        $this->post('/register', [
            'name' => 'Intruder',
            'email' => 'intruder@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertNotFound();
        $this->get('/password/reset')->assertNotFound();

        $this->assertDatabaseMissing('users', ['email' => 'intruder@example.com']);
    }

    public function test_user_without_role_cannot_enter_the_admin_panel(): void
    {
        $this->actingAs($this->userWithRole(null))->get('/dashboard')->assertForbidden();
    }

    public function test_route_access_follows_the_role_matrix(): void
    {
        $matrix = [
            '/dashboard'           => ['admin' => 200, 'writer' => 200, 'uploader' => 200],
            '/users'               => ['admin' => 200, 'writer' => 200, 'uploader' => 200],
            '/home/poster'         => ['admin' => 200, 'writer' => 403, 'uploader' => 200],
            '/home/gallery'        => ['admin' => 200, 'writer' => 403, 'uploader' => 200],
            '/home/menu'           => ['admin' => 200, 'writer' => 403, 'uploader' => 403],
            '/home/hero'           => ['admin' => 200, 'writer' => 403, 'uploader' => 403],
            '/rrfuture/hero'       => ['admin' => 200, 'writer' => 403, 'uploader' => 403],
            '/service/hero'        => ['admin' => 200, 'writer' => 403, 'uploader' => 403],
            '/contact/hero'        => ['admin' => 200, 'writer' => 403, 'uploader' => 403],
            '/blog/articles'       => ['admin' => 200, 'writer' => 200, 'uploader' => 403],
            '/blog/categories'     => ['admin' => 200, 'writer' => 200, 'uploader' => 403],
            '/blog/configs'        => ['admin' => 200, 'writer' => 403, 'uploader' => 403],
        ];

        $users = [
            'admin' => $this->userWithRole(User::ROLE_ADMIN),
            'writer' => $this->userWithRole(User::ROLE_WRITER),
            'uploader' => $this->userWithRole(User::ROLE_UPLOADER),
        ];

        foreach ($matrix as $uri => $expectations) {
            foreach ($expectations as $role => $status) {
                $this->actingAs($users[$role])->get($uri)->assertStatus($status);
            }
        }
    }

    public function test_only_admin_can_create_users(): void
    {
        $payload = [
            'name' => 'New User',
            'email' => 'new@example.com',
            'role' => User::ROLE_ADMIN,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $this->actingAs($this->userWithRole(User::ROLE_WRITER))->post('/users', $payload)->assertForbidden();
        $this->assertDatabaseMissing('users', ['email' => 'new@example.com']);

        $this->actingAs($this->userWithRole(User::ROLE_ADMIN))->post('/users', $payload)->assertRedirect();
        $this->assertDatabaseHas('users', ['email' => 'new@example.com', 'role' => User::ROLE_ADMIN]);
    }

    public function test_non_admin_cannot_escalate_their_own_role(): void
    {
        $writer = $this->userWithRole(User::ROLE_WRITER);

        $this->actingAs($writer)->put("/users/{$writer->id}", [
            'name' => 'Renamed Writer',
            'email' => $writer->email,
            'role' => User::ROLE_ADMIN,
        ])->assertRedirect();

        $writer->refresh();
        $this->assertSame('Renamed Writer', $writer->name);
        $this->assertSame(User::ROLE_WRITER, $writer->role);
    }

    public function test_non_admin_cannot_edit_or_delete_other_users(): void
    {
        $writer = $this->userWithRole(User::ROLE_WRITER);
        $admin = $this->userWithRole(User::ROLE_ADMIN);

        $this->actingAs($writer)->put("/users/{$admin->id}", [
            'name' => 'Hacked',
            'email' => $admin->email,
        ])->assertForbidden();

        $this->actingAs($writer)->delete("/users/{$admin->id}")->assertForbidden();

        $this->assertDatabaseHas('users', ['id' => $admin->id, 'name' => $admin->name]);
    }

    public function test_admin_cannot_delete_or_demote_themselves(): void
    {
        $admin = $this->userWithRole(User::ROLE_ADMIN);

        $this->actingAs($admin)->delete("/users/{$admin->id}")->assertForbidden();

        $this->actingAs($admin)->put("/users/{$admin->id}", [
            'name' => $admin->name,
            'email' => $admin->email,
            'role' => User::ROLE_WRITER,
        ])->assertRedirect();

        $this->assertSame(User::ROLE_ADMIN, $admin->fresh()->role);
    }

    public function test_admin_can_change_another_users_role_and_delete_them(): void
    {
        $admin = $this->userWithRole(User::ROLE_ADMIN);
        $writer = $this->userWithRole(User::ROLE_WRITER);

        $this->actingAs($admin)->put("/users/{$writer->id}", [
            'name' => $writer->name,
            'email' => $writer->email,
            'role' => User::ROLE_UPLOADER,
        ])->assertRedirect();

        $this->assertSame(User::ROLE_UPLOADER, $writer->fresh()->role);

        $this->actingAs($admin)->delete("/users/{$writer->id}")->assertRedirect();
        $this->assertDatabaseMissing('users', ['id' => $writer->id]);
    }

    public function test_comment_submission_is_rate_limited(): void
    {
        $route = collect(app('router')->getRoutes())->first(fn ($r) => $r->getName() === 'comment.store');

        $this->assertContains('throttle:5,1', $route->gatherMiddleware());
    }
}

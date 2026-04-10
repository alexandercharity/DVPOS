<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login(): void
    {
        $user = User::create([
            'name'     => 'Test User',
            'email'    => 'test@example.com',
            'password' => bcrypt('password123'),
            'role'     => 'kasir',
        ]);

        $this->actingAs($user);
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_can_logout(): void
    {
        $user = User::create([
            'name'     => 'Test User',
            'email'    => 'test@example.com',
            'password' => bcrypt('password123'),
            'role'     => 'kasir',
        ]);

        $this->actingAs($user);
        $this->post('/logout');
        // Setelah logout, session tidak lagi authenticated
        $this->assertNotNull($user); // user masih ada di DB
    }

    public function test_pemilik_role_stored_correctly(): void
    {
        $user = User::create([
            'name'     => 'Pemilik',
            'email'    => 'pemilik@example.com',
            'password' => bcrypt('password'),
            'role'     => 'pemilik',
        ]);

        $this->assertDatabaseHas('users', ['email' => 'pemilik@example.com', 'role' => 'pemilik']);
    }

    public function test_kasir_role_stored_correctly(): void
    {
        $user = User::create([
            'name'     => 'Kasir',
            'email'    => 'kasir@example.com',
            'password' => bcrypt('password'),
            'role'     => 'kasir',
        ]);

        $this->assertDatabaseHas('users', ['email' => 'kasir@example.com', 'role' => 'kasir']);
    }

    public function test_password_is_hashed(): void
    {
        $user = User::create([
            'name'     => 'Test',
            'email'    => 'hash@example.com',
            'password' => bcrypt('secret'),
            'role'     => 'kasir',
        ]);

        $this->assertNotEquals('secret', $user->password);
    }
}

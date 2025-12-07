<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminLoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function email_required()
    {
        $response = $this->post('/admin/login', [
            'email' => '',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function password_required()
    {
        $response = $this->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors('password');
    }

    /** @test */
    public function login_fail_wrong_credentials()
    {
        $response = $this->post('/admin/login', [
            'email' => 'unknown@example.com',
            'password' => 'wrongpass',
        ]);

        $response->assertSessionHasErrors('email'); 
    }

    /** @test */
    public function normal_user_cannot_login_as_admin()
    {
        // 一般ユーザー
        User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'user@example.com',
            'password' => 'password123',
        ]);

        // 管理者のみ許可 → 失敗を期待
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /** @test */
    public function admin_can_login_successfully()
    {
        // 管理者ユーザー
        User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(302);
        $this->assertAuthenticated();
    }
}

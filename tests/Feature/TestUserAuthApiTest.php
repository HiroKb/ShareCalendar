<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TestUserAuthApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        DB::table('users')->insert([
            'id' => config('test_user.id'),
            'name' => 'TestUser',
            'email' => config('test_user.email'),
            'image_path' => null,
            'email_verified_at' => \Illuminate\Support\Carbon::now(),
            'password' => Hash::make(config('test_user.password')),
            'provider_name' => null,
            'provider_id' => config('test_user.provider_id'),
            'provider_email' => null,
            'remember_token' => null,
            'created_at' => \Illuminate\Support\Carbon::now(),
            'updated_at' => \Illuminate\Support\Carbon::now()
        ]);
    }

    /**
     * @test
     */
    public function should_テストユーザーでログイン・ログアウト()
    {
        $response = $this->json('post', '/api/test-user/login');

        $response
            ->assertStatus(200)
            ->assertJson([
                'id' => config('test_user.id'),
                'name' => 'TestUser',
                'email' => config('test_user.email')
            ]);

        $this->assertAuthenticated();

        $response = $this->json('post', '/api/logout');
        $response->assertStatus(200);
        $this->assertGuest();
    }
}

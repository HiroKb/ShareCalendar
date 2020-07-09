<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        DB::table('admins')->insert([
            'name' => config('admin_user.name'),
            'password' => Hash::make(config('admin_user.password')),
        ]);

        $this->user = factory(User::class)->create();
    }

    public function should_通常ユーザーログイン時はリダイレクト()
    {
        $response = $this->actingAs($this->user)->post(route('admin_login'), [
            'name' => config('admin_user.name'),
            'password' => config('admin_user.password')
        ]);
        $response->assertStatus(302);
        $this->assertAuthenticated();
        $this->assertGuest('admin');
    }

    /**
     * @test
     */
    public function should_管理者アカウントでログイン・ログアウト()
    {
        $response = $this->post(route('admin_login'), [
            'name' => 'testtest',
            'password' => 'testtest'
        ]);
        $response->assertStatus(302);
        $this->assertGuest('admin');

        $response = $this->post(route('admin_login'), [
            'name' => config('admin_user.name'),
            'password' => config('admin_user.password')
        ]);

        $response->assertStatus(302)
                 ->assertRedirect(route('admin_index'));
        $this->assertAuthenticated('admin');

        $response = $this->post(route('admin_logout'));
        $this->assertGuest('admin');
    }

}

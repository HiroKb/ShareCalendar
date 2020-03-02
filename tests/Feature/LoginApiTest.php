<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

//        ダミーユーザー作成
        $this->user = factory(User::class)->create();
    }

    /**
     * @test
     */
    public function should_認証後ログインユーザーデータを返却()
    {
        $response = $this->json('post', route('login'),[
            'email' => $this->user->email,
            'password' => 'password'
        ]);

//        レスポンスが期待通りか
        $response
            ->assertStatus(200)
            ->assertJson(['name' => $this->user->name]);

//        指定したユーザーがログイン状態か
        $this->assertAuthenticatedAs($this->user);
    }
}

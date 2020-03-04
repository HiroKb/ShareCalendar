<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GetUserApiTest extends TestCase
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
    public function should_ログインしている場合ログイン中のユーザーを返却()
    {
        $response = $this->actingAs($this->user)->json('get', route('user'));

//        レスポンスが期待通りか
        $response
            ->assertStatus(200)
            ->assertJson([
                'name' => $this->user->name
            ]);
    }

    /**
     * @test
     */
    public function should_ログインしていない場合空文字を返却()
    {
        $response = $this->json('get', route('user'));

//        レスポンスが期待通りか
        $response->assertStatus(200);
        $this->assertEquals('', $response->content());
    }
}

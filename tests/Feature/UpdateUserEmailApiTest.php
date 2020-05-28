<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Tests\TestCase;

class UpdateUserEmailApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
    }

    /**
     * @test
     */
    public function should_ユーザーのメールアドレスを変更後ユーザーデータを返却()
    {

        $email = Str::random() . '@test.com';

        $response = $this->actingAs($this->user)
            ->json('patch', '/api/users/email',[
                'email' => $email,
                'password' => 'password'
            ]);


//        レスポンスが期待通りか
        $response->assertStatus(200)
                 ->assertJson([
                     'name' => $this->user->name,
                     'email' => $email
                 ]);

//        メールアドレスが正しく変更されているか
        $this->assertEquals($email, Auth::user()->email);
    }
}

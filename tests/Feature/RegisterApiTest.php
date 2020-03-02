<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function should_新しいユーザーを作成して返却()
    {
        $data = [
            'name' => 'test てすと',
            'email' => 'test@test.com',
            'password' => 'test12345678'
        ];

        $response = $this->json('post', route('register'), $data);

//        ユーザーが正しく作成されているか
        $user = User::first();
        $this->assertEquals($data['name'], $user->name);


        $response
            ->assertStatus(201)
            ->assertJson(['name' => $user->name]);
    }
}

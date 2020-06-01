<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class RegistrationPasswordApiTest extends TestCase
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
    public function should_パスワードを新規登録()
    {
        $password = Str::random();
        $response = $this->actingAs($this->user)
            ->json('post', '/api/users/password',[
                'password' => $password
            ]);
        $response->assertStatus(404);

        $this->user->password = null;
        $this->user->save();

        $response = $this->actingAs($this->user)
            ->json('post', '/api/users/password',[
                'password' => $password
            ]);
        $response->assertStatus(201);
        $this->assertTrue(Hash::check($password, Auth::user()->password));
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class UpdateUserPasswordApiTest extends TestCase
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
    public function should_ユーザーのパスワードを変更()
    {

        $newPassword = Str::random();

        $response = $this->actingAs($this->user)
            ->json('patch', '/api/users/password',[
                'current_password' => 'password',
                'new_password' => $newPassword
            ]);


        $response->assertStatus(200);
//        パスワードが正しく変更されているか
        $this->assertTrue(Hash::check($newPassword, Auth::user()->password));
    }


}

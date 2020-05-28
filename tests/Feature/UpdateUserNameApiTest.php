<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Tests\TestCase;

class UpdateUserNameApiTest extends TestCase
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
    public function should_ユーザー名を変更しユーザーデータを返却()
    {
        $newName = Str::random();

        $response = $this->actingAs($this->user)
            ->json('patch', '/api/users/name',[
                'name' => $newName
            ]);


//        レスポンスが期待通りか
        $response->assertStatus(200)
                 ->assertJson(['name' => $newName]);

//        ユーザー名が変更されているか
        $this->assertEquals($newName, Auth::user()->name);
    }

}

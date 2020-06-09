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
    public function should_メールアドレス変更テーブルにデータを追加()
    {

        $newEmail = Str::random() . '@test.com';

        $response = $this->actingAs($this->user)
            ->json('post', '/api/users/email',[
                'new_email' => $newEmail,
            ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('email_updates', ['new_email' => $newEmail]);
    }
}

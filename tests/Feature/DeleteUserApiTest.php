<?php

namespace Tests\Feature;

use App\Models\SharedCalendar;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DeleteUserApiTest extends TestCase
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
    public function shoud_ユーザーデータを削除()
    {

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
        ]);
        $response = $this->actingAs($this->user)->json('delete', '/api/users');

        $response->assertStatus(200);
        $this->assertDatabaseMissing('users', [
            'id' => $this->user->id,
        ]);
    }
}

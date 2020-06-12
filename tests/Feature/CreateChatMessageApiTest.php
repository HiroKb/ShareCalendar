<?php

namespace Tests\Feature;

use App\Models\SharedCalendar;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateChatMessageApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user1 = factory(User::class)->create();
        $this->user2 = factory(User::class)->create();
    }

    /**
     * @test
     */
    public function shoud_チャットメッセージを作成、データを返却()
    {

        $calendar = new SharedCalendar();
        $calendar->calendar_name = 'test';
        $calendar->admin_id = $this->user1->id;
        $calendar->save();
        $calendar->members()->attach([$this->user1->id]);

        $data = [
            'message' => 'test message test message'
        ];

        $response = $this->actingAs($this->user2)
            ->json('post', 'api/shared-calendars/' . $calendar->id . '/chat/messages', $data);
        $response->assertStatus(404);

        $response = $this->actingAs($this->user1)
            ->json('post', 'api/shared-calendars/' . $calendar->id . '/chat/messages', $data);
        $response
            ->assertStatus(201)
            ->assertJson([
                'message' => $data['message'],
                'posted_user_id' => $this->user1->id
            ]);

        $this->assertDatabaseHas('chat_messages', [
            'message' => $data['message'],
            'calendar_id' => $calendar->id,
            'posted_user_id' => $this->user1->id
        ]);
    }
}

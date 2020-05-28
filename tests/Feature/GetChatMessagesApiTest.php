<?php

namespace Tests\Feature;

use App\Models\ChatMessage;
use App\Models\SharedCalendar;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GetChatMessagesApiTest extends TestCase
{

    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user1 = factory(User::class)->create();
        $this->user2 = factory(User::class)->create();
        $this->user3 = factory(User::class)->create();
    }

    /**
     * @test
     */
    public function shoud_指定したカレンダーのチャットメッセージを取得()
    {
        $calendar = new SharedCalendar();
        $calendar->calendar_name = 'test';
        $calendar->admin_id = $this->user1->id;
        $calendar->save();
        $calendar->members()->attach([$this->user1->id]);
        $calendar->members()->attach([$this->user2->id]);

        $message1 = new ChatMessage();
        $message1->posted_user_id = $this->user1->id;
        $message1->message = 'test1';
        $calendar->chatMessages()->save($message1);

        sleep(1);

        $message2 = new ChatMessage();
        $message2->posted_user_id = $this->user2->id;
        $message2->message = 'test2';
        $calendar->chatMessages()->save($message2);

        sleep(1);

        $message3 = new ChatMessage();
        $message3->posted_user_id = $this->user2->id;
        $message3->message = 'test3';
        $calendar->chatMessages()->save($message3);


        $response = $this->actingAs($this->user2)
            ->json('get', '/api/shared-calendars/' . $calendar->id . '/chat/messages');

        $response
            ->assertStatus(200)
            ->assertJsonPath('0.message', 'test1')
            ->assertJsonPath('0.posted_user.name', $this->user1->name)
            ->assertJsonPath('1.message', 'test2')
            ->assertJsonPath('1.posted_user.name', $this->user2->name)
            ->assertJsonPath('2.message', 'test3')
            ->assertJsonPath('2.posted_user.name', $this->user2->name);

        $response = $this->actingAs($this->user3)
            ->json('get', '/api/shared-calendars/' . $calendar->id . '/chat/messages');
        $response->assertStatus(404);
    }
}

<?php

namespace Tests\Feature;

use App\Models\SharedCalendar;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminUnShareCalendarApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user1 = factory(User::class)->create();
        $this->user2 = factory(User::class)->create();
        $this->user3 = factory(User::class)->create();
        $this->user4 = factory(User::class)->create();
    }

    /**
     * @test
     */
    public function shoud_カレンダー共有を解除()
    {

        $calendar = new SharedCalendar();
        $calendar->calendar_name = 'test';
        $calendar->admin_id = $this->user1->id;
        $calendar->save();
        $calendar->members()->attach([$this->user1->id]);

        $calendar->members()->attach([$this->user2->id]);
        $calendar->members()->attach([$this->user3->id]);
        $calendar->members()->attach([$this->user4->id]);

        $this->assertDatabaseHas('shared_calendar_user_members',[
            'calendar_id' => $calendar->id,
            'user_id' => $this->user3->id
        ]);

//        カレンダー管理者以外が共有解除ユーザーを指定した場合
        $response = $this->actingAs($this->user2)->json('delete', '/api/shared-calendars/'. $calendar->id . '/members/' . $this->user3->id);
        $response->assertStatus(404);

//        カレンダー管理者が共有解除ユーザーを指定しなかった場合
        $response = $this->actingAs($this->user1)->json('delete', '/api/shared-calendars/'. $calendar->id . '/members');
        $response->assertStatus(404);

//        カレンダー管理者が共有解除ユーザーを指定した場合
        $response = $this->actingAs($this->user1)->json('delete', '/api/shared-calendars/'. $calendar->id . '/members/' . $this->user3->id);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('shared_calendar_user_members',[
            'calendar_id' => $calendar->id,
            'user_id' => $this->user3->id
        ]);
        $this->assertDatabaseHas('shared_calendar_user_members',[
            'calendar_id' => $calendar->id,
            'user_id' => $this->user2->id
        ]);
        $this->assertDatabaseHas('shared_calendar_user_members',[
            'calendar_id' => $calendar->id,
            'user_id' => $this->user4->id
        ]);

//        カレンダー管理者以外が共有解除ユーザーを指定しなかった場合
        $response = $this->actingAs($this->user4)->json('delete', '/api/shared-calendars/'. $calendar->id . '/members');
        $response->assertStatus(200);
        $this->assertDatabaseHas('shared_calendar_user_members',[
            'calendar_id' => $calendar->id,
            'user_id' => $this->user2->id
        ]);
        $this->assertDatabaseMissing('shared_calendar_user_members',[
            'calendar_id' => $calendar->id,
            'user_id' => $this->user4->id
        ]);
    }
}

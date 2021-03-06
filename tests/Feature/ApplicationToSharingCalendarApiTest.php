<?php

namespace Tests\Feature;

use App\Models\SharedCalendar;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ApplicationToSharingCalendarApiTest extends TestCase
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
    public function shoud_カレンダー共有申請者のデータを保存()
    {

        $calendar = new SharedCalendar();
        $calendar->calendar_name = 'test';
        $calendar->admin_id = $this->user1->id;
        $calendar->save();
        $calendar->members()->attach([$this->user1->id]);

        $response = $this->actingAs($this->user2)->json('put', '/api/shared-calendars/' . $calendar->search_id. '/applications');

        $response->assertStatus(201);
        $this->assertDatabaseHas('shared_calendar_user_applications',[
            'calendar_id' => $calendar->id,
            'user_id' => $this->user2->id
            ]);

        $response = $this->actingAs($this->user2)->json('put', '/api/shared-calendars/' . $calendar->search_id. '/applications');

        $response->assertStatus(404);
        $this->assertDatabaseHas('shared_calendar_user_applications',[
            'calendar_id' => $calendar->id,
            'user_id' => $this->user2->id
        ]);
    }
}

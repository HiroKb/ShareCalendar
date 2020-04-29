<?php

namespace Tests\Feature;

use App\SharedCalendar;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpdateSharedCalendarNameApiTest extends TestCase
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
    public function shoud_指定した共有カレンダーの名前を変更()
    {
        $calendar = new SharedCalendar();
        $calendar->calendar_name = 'test';
        $calendar->admin_id = $this->user1->id;
        $calendar->save();
        $calendar->members()->attach([$this->user1->id]);
        $calendar->members()->attach([$this->user2->id]);

        $data = [
            'calendar_name' => 'test1234'
        ];

        $response = $this->actingAs($this->user1)->json('patch', '/api/shared-calendars/' . $calendar->id . '/name', $data);

        $changedCalendar = SharedCalendar::find($calendar->id);

        $this->assertEquals($data['calendar_name'], $changedCalendar->calendar_name);
        $response->assertStatus(200)
            ->assertJson([
                'calendar_name' => $changedCalendar->calendar_name,
                'search_id' => $changedCalendar->search_id
            ]);

        $response = $this->actingAs($this->user2)->json('patch', '/api/shared-calendars/' . $calendar->id . '/name', $data);
        $response->assertStatus(404);
    }
}

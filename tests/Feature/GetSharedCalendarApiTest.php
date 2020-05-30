<?php

namespace Tests\Feature;

use App\Models\SharedCalendar;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GetSharedCalendarApiTest extends TestCase
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
    public function shoud_指定した共有カレンダーデータを取得()
    {

        $calendar = new SharedCalendar();
        $calendar->calendar_name = 'test';
        $calendar->admin_id = $this->user1->id;
        $calendar->save();
        $calendar->members()->attach([$this->user1->id]);
        $calendar->members()->attach([$this->user2->id]);

        $response = $this->actingAs($this->user1)->json('get', '/api/shared-calendars/' . $calendar->id);

        $response->assertStatus(200)
                 ->assertJson([
                     'calendar_name' => $calendar->calendar_name,
                     'search_id' => $calendar->search_id
                 ]);

        $response = $this->actingAs($this->user2)->json('get', '/api/shared-calendars/' . $calendar->id);

        $response->assertStatus(200)
            ->assertJsonMissingExact([
                'search_id' => $calendar->search_id
            ]);

        $response = $this->actingAs($this->user3)->json('get', '/api/shared-calendars/' . $calendar->id);

        $response->assertStatus(404);
    }
}

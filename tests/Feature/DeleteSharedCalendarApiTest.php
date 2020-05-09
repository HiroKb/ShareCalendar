<?php

namespace Tests\Feature;

use App\SharedCalendar;
use App\SharedSchedule;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DeleteSharedCalendarApiTest extends TestCase
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
    public function shoud_指定した共有カレンダーを削除()
    {
        $calendar = new SharedCalendar();
        $calendar->calendar_name = 'test';
        $calendar->admin_id = $this->user1->id;
        $calendar->save();
        $calendar->members()->attach([$this->user1->id]);
        $calendar->members()->attach([$this->user2->id]);

        $response = $this->actingAs($this->user2)->json('delete', '/api/shared-calendars/' . $calendar->id);
        $response->assertStatus(404);

        $this->assertDatabaseHas('shared_calendars', [
            'id' => $calendar->id,
        ]);

        $response = $this->actingAs($this->user1)->json('delete', '/api/shared-calendars/' . $calendar->id);
        $response->assertStatus(200);
        $this->assertDatabaseMissing('shared_calendars', [
            'id' => $calendar->id,
        ]);
    }
}
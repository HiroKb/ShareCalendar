<?php

namespace Tests\Feature;

use App\Models\SharedCalendar;
use App\Models\SharedSchedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpdateSharedScheduleApiTest extends TestCase
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
    public function shoud_指定した共有スケジュールを更新()
    {
        $calendar = new SharedCalendar();
        $calendar->calendar_name = 'test';
        $calendar->admin_id = $this->user1->id;
        $calendar->save();
        $calendar->members()->attach([$this->user1->id]);
        $calendar->members()->attach([$this->user2->id]);

        $schedule1 = new SharedSchedule();
        $schedule1->date = '2020-04-30';
        $schedule1->time = '13:35';
        $schedule1->title = 'test1';
        $schedule1->description = 'test1';

        $schedule2 = new SharedSchedule();
        $schedule2->date = '2020-04-20';
        $schedule2->time = '13:35';
        $schedule2->title = 'test2';
        $schedule2->description = 'test2';

        $schedule3 = new SharedSchedule();
        $schedule3->date = '2020-04-20';
        $schedule3->time = '13:25';
        $schedule3->title = 'test3';
        $schedule3->description = 'test3';

        $calendar->schedules()->save($schedule1);
        $calendar->schedules()->save($schedule2);
        $calendar->schedules()->save($schedule3);

        $data = [
            'time' => '23:45:00',
            'title' => 'updated',
            'description' => 'updated'
        ];
        $response = $this->actingAs($this->user3)
            ->json('patch', '/api/shared-calendars/' . $calendar->id . '/schedules/' . $schedule2->id, $data);
        $response->assertStatus(404);
        $this->assertDatabaseMissing('shared_schedules', $data);


        $updatedData = [
            'id' => $schedule1->id,
            'date' => $schedule1->date,
            'time' => '23:45:00',
            'title' => 'updated',
            'description' => 'updated'
        ];
        $response = $this->actingAs($this->user2)
            ->json('patch', 'api/shared-calendars/' . $calendar->id . '/schedules/' . $schedule1->id, $data);
        $response
            ->assertStatus(200)
            ->assertJson($updatedData);
        $this->assertDatabaseHas('shared_schedules', $updatedData);
    }
}

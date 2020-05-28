<?php

namespace Tests\Feature;

use App\Models\Schedule;
use App\Models\SharedCalendar;
use App\Models\SharedSchedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class CreateSharedScheduleApiTest extends TestCase
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
    public function shoud_共有スケジュールを作成()
    {

        $calendar = new SharedCalendar();
        $calendar->calendar_name = 'test';
        $calendar->admin_id = $this->user1->id;
        $calendar->save();
        $calendar->members()->attach([$this->user1->id]);
        $calendar->members()->attach([$this->user2->id]);

        $data = [
            'date' => Carbon::now()->format('Y-m-d'),
            'time' => Carbon::now()->format('H:i:s'),
            'title' => 'test title test title',
            'description' => 'test description test description'
        ];

        $response = $this->actingAs($this->user2)
                         ->json('post', 'api/shared-calendars/' . $calendar->id . '/schedules', $data);

        $schedule = SharedSchedule::first();
        $this->assertEquals($calendar->id, $schedule->calendar_id);
        $this->assertEquals($data['date'], $schedule->date);
        $this->assertEquals($data['time'], $schedule->time);
        $this->assertEquals($data['title'], $schedule->title);
        $this->assertEquals($data['description'], $schedule->description);

        $response
            ->assertStatus(201)
            ->assertJson([
                'id' => $schedule->id,
                'date' => $data['date'],
                'time' => $data['time'],
                'title' => $data['title'],
                'description' => $data['description']
            ]);

        $response = $this->actingAs($this->user3)
            ->json('post', 'api/shared-calendars/' . $calendar->id . '/schedules', $data);
        $response->assertStatus(404);
    }
}

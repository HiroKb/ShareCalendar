<?php

namespace Tests\Feature;

use App\Schedule;
use App\SharedCalendar;
use App\SharedSchedule;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class GetScheduleApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
//        ダミーユーザー作成
        $this->user = factory(User::class)->create();
    }

    /**
     * @test
     */
    public function should_ログインユーザーの登録スケジュールを返却()
    {


        $schedule1 = new Schedule();
        $schedule1->date = '2020-04-30';
        $schedule1->time = '13:35';
        $schedule1->title = 'test1';
        $schedule1->description = 'test1';

        $schedule2 = new Schedule();
        $schedule2->date = '2020-04-20';
        $schedule2->time = '13:35';
        $schedule2->title = 'test2';
        $schedule2->description = 'test2';

        $schedule3 = new Schedule();
        $schedule3->date = '2020-04-20';
        $schedule3->time = '13:25';
        $schedule3->title = 'test3';
        $schedule3->description = 'test3';

        $this->user->schedules()->save($schedule1);
        $this->user->schedules()->save($schedule2);
        $this->user->schedules()->save($schedule3);


        $calendar = new SharedCalendar();
        $calendar->calendar_name = 'test';
        $calendar->admin_id = $this->user->id;
        $calendar->save();
        $calendar->members()->attach([$this->user->id]);

        $sharedSchedule1 = new SharedSchedule();
        $sharedSchedule1->date = '2020-04-15';
        $sharedSchedule1->time = '13:35';
        $sharedSchedule1->title = 'test4';
        $sharedSchedule1->description = 'test4';

        $sharedSchedule2 = new SharedSchedule();
        $sharedSchedule2->date = '2020-04-20';
        $sharedSchedule2->time = '13:30';
        $sharedSchedule2->title = 'test5';
        $sharedSchedule2->description = 'test5';

        $sharedSchedule3 = new SharedSchedule();
        $sharedSchedule3->date = '2020-04-20';
        $sharedSchedule3->time = null;
        $sharedSchedule3->title = 'test6';
        $sharedSchedule3->description = 'test6';

        $calendar->schedules()->save($sharedSchedule1);
        $calendar->schedules()->save($sharedSchedule2);
        $calendar->schedules()->save($sharedSchedule3);



        $response = $this->actingAs($this->user)->json('get', '/api/schedules/2020-03-29/2020-05-02');

        $response
            ->assertStatus(200)
            ->assertJsonPath('2020-04-15.0.title', 'test4')
            ->assertJsonPath('2020-04-20.0.title', 'test6')
            ->assertJsonPath('2020-04-20.1.title', 'test3')
            ->assertJsonPath('2020-04-20.2.title', 'test5')
            ->assertJsonPath('2020-04-20.3.title', 'test2')
            ->assertJsonPath('2020-04-30.0.title', 'test1');

    }
}

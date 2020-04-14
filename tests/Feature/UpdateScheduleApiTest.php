<?php

namespace Tests\Feature;

use App\Schedule;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpdateScheduleApiTest extends TestCase
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
    public function should_指定されたスケジュールを更新()
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

        // スケジュールが登録されていることを確認
        $this->assertDatabaseHas('schedules', ['id' => $schedule2->id]);

        $data = [
            'time' => '20:55:00',
            'title' => 'test22',
            'description' => 'description22'
        ];
        $response = $this->actingAs($this->user)->json('patch', '/api/schedules/' . $schedule2->id, $data);

//        レスポンスが期待通りか
        $response
            ->assertStatus(200)
            ->assertJson([
                'id' => $schedule2->id,
                'date' => $schedule2->date,
                'time' => $data['time'],
                'title' => $data['title'],
                'description' => $data['description'],
            ]);

        $updatedSchedule = Schedule::find($schedule2->id);

        $this->assertEquals($updatedSchedule->time, $data['time']);
        $this->assertEquals($updatedSchedule->title, $data['title']);
        $this->assertEquals($updatedSchedule->description, $data['description']);
    }
}

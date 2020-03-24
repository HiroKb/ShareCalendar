<?php

namespace Tests\Feature;

use App\Schedule;
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

        $response = $this->actingAs($this->user)->json('get', '/api/schedule/2020-03-29/2020-05-02');

        $response
            ->assertStatus(200)
            ->assertJsonPath('0.title', 'test3')
            ->assertJsonPath('1.title', 'test2')
            ->assertJsonPath('2.title', 'test1');



    }
}

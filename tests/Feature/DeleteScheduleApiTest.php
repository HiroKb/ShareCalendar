<?php

namespace Tests\Feature;

use App\Models\Schedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class DeleteScheduleApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
//        ダミーユーザー作成
        $this->user1 = factory(User::class)->create();
        $this->user2 = factory(User::class)->create();
    }

    /**
     * @test
     */
    public function should_指定されたスケジュールを削除()
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

        $this->user1->schedules()->save($schedule1);
        $this->user1->schedules()->save($schedule2);
        $this->user1->schedules()->save($schedule3);

        // スケジュールが登録されていることを確認
        $this->assertDatabaseHas('schedules', ['id' => $schedule2->id]);

        $response = $this->actingAs($this->user1)->json('delete', '/api/schedules/' . $schedule2->id);

//        レスポンスが期待通りか
        $response->assertStatus(200);

        // スケジュールが削除されているか
        $this->assertDatabaseMissing('schedules', ['id' => $schedule2->id]);

        $response = $this->actingAs($this->user2)->json('delete', '/api/schedules/' . $schedule3->id);

        $response->assertStatus(404);
        $this->assertDatabaseHas('schedules', ['id' => $schedule3->id]);
    }
}

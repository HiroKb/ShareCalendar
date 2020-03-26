<?php

namespace Tests\Feature;

use App\Schedule;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class CreateScheduleApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
    }

    /**
     * @test
     */
    public function should_スケジュールを登録して登録したデータを返却()
    {
        $data = [
            'date' => Carbon::now()->format('Y-m-d'),
            'time' => Carbon::now()->format('H:i:s'),
            'title' => 'test title test title',
            'description' => 'test description test description'
        ];

        $response = $this->actingAs($this->user)
                         ->json('post', '/api/schedule', $data);

//        レスポンスが期待通りか
        $response
            ->assertStatus(201)
            ->assertJson([
                'date' => $data['date'],
                'time' => $data['time'],
                'title' => $data['title'],
                'description' => $data['description']
                ]);

        $schedule = Schedule::first();
        $this->assertEquals(Auth::user()->id, $schedule->user_id);
        $this->assertEquals($data['date'], $schedule->date);
        $this->assertEquals($data['time'], $schedule->time);
        $this->assertEquals($data['title'], $schedule->title);
        $this->assertEquals($data['description'], $schedule->description);
    }
}

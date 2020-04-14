<?php

namespace Tests\Feature;

use App\SharedCalendar;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Tests\TestCase;

class CreateSharedCalendarApiTest extends TestCase
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
    public function should_共有カレンダーを作成しデータを返却()
    {
        $data = [
            'calendar_name' => Str::random()
        ];

        $response = $this->actingAs($this->user)
            ->json('post', '/api/shared-calendars', $data);

//        レスポンスが期待通りか
        $response
            ->assertStatus(201)
            ->assertJson([
                'calendar_name' => $data['calendar_name'],
                'admin_id' => $this->user->id
            ]);

        $calendar = SharedCalendar::first();
        $this->assertEquals($data['calendar_name'], $calendar->calendar_name);
        $this->assertEquals(Auth::user()->id, $calendar->admin_id);
    }
}

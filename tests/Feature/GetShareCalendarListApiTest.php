<?php

namespace Tests\Feature;

use App\SharedCalendar;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GetShareCalendarListApiTest extends TestCase
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
    public function should_参加している共有カレンダーを返却()
    {
        $calendar1 = new SharedCalendar();
        $calendar1->calendar_name = 'test1';
        $calendar1->admin_id = $this->user->id;
        $calendar1->save();
        $calendar1->members()->attach([$this->user->id]);

        sleep(1);

        $calendar2 = new SharedCalendar();
        $calendar2->calendar_name = 'test2';
        $calendar2->admin_id = $this->user->id;
        $calendar2->save();
        $calendar2->members()->attach([$this->user->id]);

        sleep(1);

        $calendar3 = new SharedCalendar();
        $calendar3->calendar_name = 'test3';
        $calendar3->admin_id = $this->user->id;
        $calendar3->save();
        $calendar3->members()->attach([$this->user->id]);


        $response = $this->actingAs($this->user)->json('get', '/api/shared-calendar/list');


        $response
            ->assertStatus(200)
            ->assertJsonPath('0.calendar_name', 'test1')
            ->assertJsonPath('1.calendar_name', 'test2')
            ->assertJsonPath('2.calendar_name', 'test3');


    }
}

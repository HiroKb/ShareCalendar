<?php

namespace Tests\Feature;

use App\SharedCalendar;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GetSharedCalendarMembersListApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user1 = factory(User::class)->create();
        $this->user2 = factory(User::class)->create();
        $this->user3 = factory(User::class)->create();
        $this->user4 = factory(User::class)->create();
        $this->user5 = factory(User::class)->create();
    }

    /**
     * @test
     */
    public function shoud_カレンダーの共有メンバーデータを返却()
    {

        $calendar = new SharedCalendar();
        $calendar->calendar_name = 'test';
        $calendar->admin_id = $this->user1->id;
        $calendar->save();

        $calendar->members()->attach([$this->user1->id]);
        sleep(1);
        $calendar->members()->attach([$this->user2->id]);
        sleep(1);
        $calendar->members()->attach([$this->user3->id]);
        sleep(1);
        $calendar->members()->attach([$this->user4->id]);

        $response = $this->actingAs($this->user2)->json('get', '/api/shared-calendars/' . $calendar->id .'/members');

        $response
            ->assertStatus(200)
            ->assertJsonPath('0.name', $this->user1->name)
            ->assertJsonPath('1.name', $this->user2->name)
            ->assertJsonPath('2.name', $this->user3->name)
            ->assertJsonPath('3.name', $this->user4->name);

        $response = $this->actingAs($this->user5)->json('get', '/api/shared-calendars/' . $calendar->id .'/members');

        $response->assertStatus(404);
    }
}

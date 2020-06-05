<?php

namespace Tests\Feature;

use App\Models\SharedCalendar;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SearchSharedCalendarApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();


        $this->user1 = factory(User::class)->create();
        $this->user2 = factory(User::class)->create();
    }

    /**
     * @test
     */
    public function shoud_検索した共有カレンダーデータを返却()
    {

        $calendar = new SharedCalendar();
        $calendar->calendar_name = 'test';
        $calendar->admin_id = $this->user1->id;
        $calendar->save();
        $calendar->members()->attach([$this->user1->id]);

        $response = $this->actingAs($this->user2)->json('get', '/api/shared-calendars/' . $calendar->search_id . '/search');
        $response->assertStatus(200)
            ->assertJson([
                'isApplicable' => true,
                'search_id' => $calendar->search_id,
                'admin_name' => $this->user1->name
            ]);


        $response = $this->actingAs($this->user1)->json('get', '/api/shared-calendars/' . $calendar->search_id . '/search');
        $response->assertStatus(200)
            ->assertJson([
                'isApplicable' => false
            ]);
    }
}

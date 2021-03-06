<?php

namespace Tests\Feature;

use App\Models\SharedCalendar;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RejectSharingApplicationApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user1 = factory(User::class)->create();
        $this->user2 = factory(User::class)->create();
        $this->user3 = factory(User::class)->create();
        $this->user4 = factory(User::class)->create();
    }

    /**
     * @test
     */
    public function shoud_指定したカレンダー共有申請者の申請を拒否()
    {

        $calendar = new SharedCalendar();
        $calendar->calendar_name = 'test';
        $calendar->admin_id = $this->user1->id;
        $calendar->save();
        $calendar->members()->attach([$this->user1->id]);

        $calendar->applicants()->attach([$this->user2->id]);
        $calendar->applicants()->attach([$this->user3->id]);
        $calendar->applicants()->attach([$this->user4->id]);

        $this->assertDatabaseHas('shared_calendar_user_applications',[
            'calendar_id' => $calendar->id,
            'user_id' => $this->user3->id
        ]);


        $response = $this->actingAs($this->user1)->json('delete', '/api/shared-calendars/'. $calendar->id . '/applications', [
            'id_list' => [
                $this->user3->id
            ]]);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('shared_calendar_user_applications',[
            'calendar_id' => $calendar->id,
            'user_id' => $this->user3->id
        ]);


        $response = $this->actingAs($this->user4)->json('delete', '/api/shared-calendars/'. $calendar->id. '/applications', [
            'id_list' => [
                $this->user4->id
            ]]);

        $response->assertStatus(404);

        $this->assertDatabaseHas('shared_calendar_user_applications',[
            'calendar_id' => $calendar->id,
            'user_id' => $this->user4->id
        ]);

    }
}

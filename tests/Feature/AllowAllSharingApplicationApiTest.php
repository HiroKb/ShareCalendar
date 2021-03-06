<?php

namespace Tests\Feature;

use App\Models\SharedCalendar;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AllowAllSharingApplicationApiTest extends TestCase
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
    public function shoud_カレンダー共有申請者の申請を全て許可()
    {

        $calednar1 = new SharedCalendar();
        $calednar1->calendar_name = 'test';
        $calednar1->admin_id = $this->user1->id;
        $calednar1->save();
        $calednar1->members()->attach([$this->user1->id]);

        $calednar1->applicants()->attach([$this->user2->id]);
        $calednar1->applicants()->attach([$this->user3->id]);
        $calednar1->applicants()->attach([$this->user4->id]);


        $this->assertDatabaseHas('shared_calendar_user_applications',[
            'calendar_id' => $calednar1->id,
            'user_id' => $this->user3->id
        ]);

        $calednar2 = new SharedCalendar();
        $calednar2->calendar_name = 'test';
        $calednar2->admin_id = $this->user1->id;
        $calednar2->save();
        $calednar2->members()->attach([$this->user1->id]);

        $calednar2->applicants()->attach([$this->user2->id]);

//        共有カレンダー管理者以外のアクセスの場合404
        $response = $this->actingAs($this->user3)->json('put', '/api/shared-calendars/'. $calednar1->id . '/members',[
            'id_list' => [
                $this->user3->id
            ]]);
        $response->assertStatus(404);
        $this->assertDatabaseHas('shared_calendar_user_applications',[
            'calendar_id' => $calednar1->id,
            'user_id' => $this->user3->id
        ]);

//        申請されていないユーザーIDを送った場合404
        $response = $this->actingAs($this->user1)->json('put', '/api/shared-calendars/'. $calednar1->id . '/members',[
            'id_list' => [
                $this->user5->id,
            ]]);
        $response->assertStatus(404);


        $response = $this->actingAs($this->user1)->json('put', '/api/shared-calendars/'. $calednar1->id . '/members',[
            'id_list' => [
                $this->user2->id,
                $this->user3->id,
                $this->user4->id,
                ]]);
        $response->assertStatus(201);
        $this->assertDatabaseMissing('shared_calendar_user_applications',[
            'calendar_id' => $calednar1->id,
            'user_id' => $this->user2->id
        ]);
        $this->assertDatabaseMissing('shared_calendar_user_applications',[
            'calendar_id' => $calednar1->id,
            'user_id' => $this->user3->id
        ]);
        $this->assertDatabaseMissing('shared_calendar_user_applications',[
            'calendar_id' => $calednar1->id,
            'user_id' => $this->user4->id
        ]);
        $this->assertDatabaseHas('shared_calendar_user_members',[
            'calendar_id' => $calednar1->id,
            'user_id' => $this->user2->id
        ]);
        $this->assertDatabaseHas('shared_calendar_user_members',[
            'calendar_id' => $calednar1->id,
            'user_id' => $this->user3->id
        ]);
        $this->assertDatabaseHas('shared_calendar_user_members',[
            'calendar_id' => $calednar1->id,
            'user_id' => $this->user4->id
        ]);

        $this->assertDatabaseHas('shared_calendar_user_applications',[
            'calendar_id' => $calednar2->id,
            'user_id' => $this->user2->id
        ]);


    }
}

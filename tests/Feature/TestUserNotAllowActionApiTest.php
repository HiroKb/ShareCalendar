<?php

namespace Tests\Feature;

use App\models\EmailUpdate;
use App\Models\Schedule;
use App\Models\SharedCalendar;
use App\Models\SharedSchedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TestUserNotAllowActionApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private $normalUser;
    private $testUser;
    private $schedule;
    private $calendar;
    private $sharedSchedule;

    protected function setUp(): void
    {
        parent::setUp();


        DB::table('users')->insert([
            'id' => config('test_user.id'),
            'name' => 'TestUser',
            'email' => config('test_user.email'),
            'image_path' => null,
            'email_verified_at' => \Illuminate\Support\Carbon::now(),
            'password' => Hash::make(config('test_user.password')),
            'provider_name' => null,
            'provider_id' => config('test_user.provider_id'),
            'provider_email' => null,
            'remember_token' => null,
            'created_at' => \Illuminate\Support\Carbon::now(),
            'updated_at' => \Illuminate\Support\Carbon::now()
        ]);

        $this->testUser = User::first();
        $this->normalUser = factory(User::class)->create();

        $schedule = new Schedule();
        $this->testUser->schedules()->save($schedule->fill([
            'date' => Carbon::now()->format('Y-m-d'),
            'time' => null,
            'title' => $this->faker->word,
            'description' => null
        ]));
        $this->schedule = Schedule::first();

        $calendar = new SharedCalendar();
        $calendar->calendar_name = $this->faker->word;
        $calendar->admin_id = $this->testUser->id;
        $calendar->image_path = null;
        $calendar->save();
        $calendar->members()->attach([$this->testUser->id]);
        $calendar->members()->attach([$this->normalUser->id]);
        $this->calendar = SharedCalendar::first();

        $sharedSchedule = new SharedSchedule();
        $this->calendar->schedules()->save($sharedSchedule->fill([
            'date' => Carbon::now()->format('Y-m-d'),
            'time' => null,
            'title' => $this->faker->word,
            'description' => null
        ]));
        $this->sharedSchedule = SharedSchedule::first();
    }

    /**
     * @test
     */
    public function should_テストユーザーでのリソースの削除やパスワード・アドレス変更等を拒否()
    {
        $response = $this->actingAs($this->testUser)->json('post', '/api/email/resend');
        $response->assertStatus(404);

        $response = $this->actingAs($this->testUser)->json('post', '/api/users/email', ['new_email' => $this->faker->email]);
        $response->assertStatus(404);
        $this->assertNull(EmailUpdate::first());

        $response = $this->actingAs($this->testUser)->json('delete', '/api/users');
        $response->assertStatus(404);
        $this->assertDatabaseHas('users',['id' => config('test_user.id')]);

        $response = $this->actingAs($this->testUser)->json('delete', '/api/schedules/'.$this->schedule->id);
        $response->assertStatus(404);
        $this->assertDatabaseHas('schedules',['id' => $this->schedule->id]);


        $response = $this->actingAs($this->testUser)->json('delete', '/api/shared-calendars/'.$this->calendar->id);
        $response->assertStatus(404);
        $this->assertDatabaseHas('shared_calendars',['id' => $this->calendar->id]);

        $response = $this->actingAs($this->testUser)->json('delete', '/api/shared-calendars/'.$this->calendar->id. '/members/'. $this->normalUser->id);
        $response->assertStatus(404);
        $this->assertDatabaseHas('shared_calendar_user_members',['user_id' => $this->normalUser->id]);


        $response = $this->actingAs($this->testUser)->json('delete', '/api/shared-calendars/'.$this->calendar->id. '/schedules/'. $this->sharedSchedule->id);
        $this->assertDatabaseHas('shared_schedules',['id' => $this->sharedSchedule->id]);
    }
}

<?php

namespace Tests\Feature;

use App\Models\SharedCalendar;
use App\Models\SharedSchedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DeleteSharedCalendarApiTest extends TestCase
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
    public function shoud_共有カレンダーを削除()
    {
        Storage::fake('s3');
        $uploadPath = Storage::disk('s3')->putFile('', UploadedFile::fake()->image('test.jpg', 1980, 1080)->size(2000), 'public');

        $calendar = new SharedCalendar();
        $calendar->calendar_name = 'test';
        $calendar->admin_id = $this->user1->id;
        $calendar->image_path = $uploadPath;
        $calendar->save();
        $calendar->members()->attach([$this->user1->id]);
        $calendar->members()->attach([$this->user2->id]);

        $response = $this->actingAs($this->user2)->json('delete', '/api/shared-calendars/' . $calendar->id);
        $response->assertStatus(404);

        $this->assertDatabaseHas('shared_calendars', [
            'id' => $calendar->id,
        ]);
        Storage::disk('s3')->assertExists($uploadPath);

        $response = $this->actingAs($this->user1)->json('delete', '/api/shared-calendars/' . $calendar->id);
        $response->assertStatus(200);
        $this->assertDatabaseMissing('shared_calendars', [
            'id' => $calendar->id,
        ]);
        Storage::disk('s3')->assertMissing($uploadPath);
    }
}

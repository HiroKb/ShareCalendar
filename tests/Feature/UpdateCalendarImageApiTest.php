<?php

namespace Tests\Feature;

use App\Models\SharedCalendar;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UpdateCalendarImageApiTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->user1 = factory(User::class)->create();
        $this->user2 = factory(User::class)->create();
    }

    /**
     * @test
     */
    public function should_カレンダーのイメージ画像を更新()
    {
        $calendar = new SharedCalendar();
        $calendar->calendar_name = 'test';
        $calendar->admin_id = $this->user1->id;
        $calendar->save();
        $calendar->members()->attach([$this->user1->id]);
        $calendar->members()->attach([$this->user2->id]);
        Storage::fake('s3');

        $response = $this->actingAs($this->user2)
            ->json('post', '/api/shared-calendars/' . $calendar->id . '/image', [
                'image' => UploadedFile::fake()->image('test.jpg', 1980, 1080)->size(2000)
            ]);
        $response->assertStatus(404);

        $response = $this->actingAs($this->user1)
            ->json('post', '/api/shared-calendars/'. $calendar->id. '/image', [
                'image' => UploadedFile::fake()->image('test.jpg', 1980, 1080)->size(2000)
            ]);

        $firstUpdatedCalendar = SharedCalendar::find($calendar->id);
        Storage::disk('s3')->assertExists($firstUpdatedCalendar->image_path);
        $response->assertStatus(201)
            ->assertJson([
                'image_url' => Storage::disk('s3')->url($firstUpdatedCalendar->image_path)
            ]);

        $response = $this->actingAs($this->user1)
            ->json('post', '/api/shared-calendars/'. $calendar->id. '/image', [
                'image' => UploadedFile::fake()->image('test.jpg', 1980, 1080)->size(2000)
            ]);

        $secondUpdatedCalendar = SharedCalendar::find($calendar->id);
        Storage::disk('s3')->assertMissing($firstUpdatedCalendar->image_path);
        Storage::disk('s3')->assertExists($secondUpdatedCalendar->image_path);

        $response->assertStatus(201)
            ->assertJson([
                'image_url' => Storage::disk('s3')->url($secondUpdatedCalendar->image_path)
            ]);
    }
}

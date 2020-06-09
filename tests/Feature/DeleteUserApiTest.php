<?php

namespace Tests\Feature;

use App\Models\SharedCalendar;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DeleteUserApiTest extends TestCase
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
    public function shoud_ユーザーデータを削除()
    {
        Storage::fake('s3');

        $uploadPath = Storage::disk('s3')->putFile('', UploadedFile::fake()->image('test.jpg', 1980, 1080)->size(2000), 'public');
        $this->user->image_path = $uploadPath;
        $this->user->save;

        Storage::disk('s3')->assertExists($uploadPath);
        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->json('delete', '/api/users');

        $response->assertStatus(200);
        $this->assertDatabaseMissing('users', [
            'id' => $this->user->id,
        ]);
        Storage::disk('s3')->assertMissing($uploadPath);
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UpdateUserImageApiTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
    }

    /**
     * @test
     */
    public function should_ユーザーのイメージ画像を更新()
    {
        Storage::fake('s3');

        $response = $this->actingAs($this->user)
            ->json('post', '/api/users/image', [
                'image' => UploadedFile::fake()->image('test.jpg', 1980, 1080)->size(2000)
            ]);

        $firstUpdatedUser = User::find($this->user->id);
        Storage::disk('s3')->assertExists($firstUpdatedUser->image_path);

        $response->assertStatus(201)
            ->assertJson([
                'image_url' => Storage::disk('s3')->url($firstUpdatedUser->image_path)
            ]);

        $response = $this->actingAs($this->user)
            ->json('post', '/api/users/image', [
                'image' => UploadedFile::fake()->image('test.jpg', 1980, 1080)->size(2000)
            ]);

        $secondUpdatedUser = User::find($this->user->id);
        Storage::cloud()->assertMissing($firstUpdatedUser->image_path);
        Storage::cloud()->assertExists($secondUpdatedUser->image_path);

        $response->assertStatus(201)
            ->assertJson([
                'image_url' => Storage::disk('s3')->url($secondUpdatedUser->image_path)
            ]);
    }
}

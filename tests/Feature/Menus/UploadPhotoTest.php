<?php

namespace Tests\Feature\Menus;

use App\Models\Menu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use PHPUnit\Framework\Attributes\Test;
use Storage;
use Tests\TestCase;
use Tests\Traits\AuthUser;

class UploadPhotoTest extends TestCase
{
    use RefreshDatabase, AuthUser;

    #[Test]
    public function can_update_photo_menu()
    {
        $this->authenticateUser(['menu:update']);

        $menu = Menu::factory()->create();
        $file = UploadedFile::fake()->image('photo.png');

        Storage::fake('public');

        $url = route('api.menus.update.photo', $menu);

        $response = $this->withHeaders([
            'Content-Type' => 'multipart/form-data',
            'Accept' => 'application/vnd.api+json'
        ])->post($url, [
            'photo' => $file,
        ]);

        $response->assertOk();

        $menu->refresh();
        Storage::disk('public')->assertExists('menus/photos/' . $menu->photo);

        $response->assertJsonApiResource($menu, ['photo_url' => $menu->photo_url]);
    }

    #[Test]
    public function photo_invalid()
    {
        $this->authenticateUser(['menu:update']);

        $menu = Menu::factory()->create();
        $file = UploadedFile::fake()->image('photo.invalid');

        Storage::fake('public');

        $url = route('api.menus.update.photo', $menu);

        $this->withHeaders([
            'Content-Type' => 'multipart/form-data',
            'Accept' => 'application/vnd.api+json'
        ])->post($url, [
            'photo' => $file,
        ])->assertJsonFragment([
            'source' => ['pointer' => "/photo"]
        ])->assertStatus(422);
    }
}

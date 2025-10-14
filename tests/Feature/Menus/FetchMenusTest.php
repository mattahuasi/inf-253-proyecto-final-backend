<?php

namespace Tests\Feature\Menus;

use App\Models\Menu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\AuthUser;

class FetchMenusTest extends TestCase
{
    use RefreshDatabase, AuthUser;

    #[Test]
    public function can_fetch_a_single_menu(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['menu:show']);

        $menu = Menu::factory()->create();

        $response = $this->getJson(route('api.menus.show', $menu));

        $response->assertJsonApiResource($menu, [
            'name' => $menu->name,
            'slug' => $menu->slug,
            'description' => $menu->description,
            'price' => $menu->price,
            'photo_url' => $menu->photo_url,
            'stock' => $menu->stock,
            'priority' => $menu->priority,
            'enabled' => $menu->enabled
        ])->assertJsonApiRelationshipLinks($menu, ['category']);
    }

    #[Test]
    public function it_returns_a_json_api_error_object_when_an_menu_is_not_found(): void
    {
        $this->authenticateUser(['menu:show']);
        $url = route('api.menus.show', [
            'menu' => 'not-existing'
        ]);

        $this->getJson($url)
            ->assertJsonApiError(
                title: trans('httpCodes.404'),
                detail: "No records found with the id 'not-existing' in the 'menus' resource.",
                status: "404"
            );
    }

    #[Test]
    public function can_fetch_all_menus(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['menu:index']);

        $menus = Menu::factory()->count(3)->create();

        $response = $this->getJson(route('api.menus.index'));

        $response->assertJsonApiResourceCollection($menus, [
            'name',
            'description',
            'price',
            'photo_url'
        ]);
    }
}

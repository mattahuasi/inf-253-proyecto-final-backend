<?php

namespace Tests\Feature\Menus;

use App\Models\Category;
use App\Models\Menu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\AuthUser;

class CategoryRelationshipTest extends TestCase
{
    use RefreshDatabase, AuthUser;

    #[Test]
    public function can_fetch_category_resource(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['menu:show']);

        $menu = Menu::factory()->create();
        $response = $this->getJson(route('api.menus.category', $menu));
        $response->assertJsonApiResource($menu->category, [
            'name' => $menu->category->name
        ]);
    }

    #[Test]
    public function can_fetch_category_relationship(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['menu:show']);

        $menu = Menu::factory()->create();
        $response = $this->getJson(route('api.menus.relationships.category.show', $menu));
        $response->assertExactJson([
            'data' => [
                'id' => $menu->category->getRouteKey(),
                'type' => $menu->category->getResourceType()
            ]
        ]);
    }

    #[Test]
    public function can_update_category_relationship(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['menu:update']);

        $menu = Menu::factory()->create();
        $category = Category::factory()->create();

        $this->withoutJsonApiDocumentFormatting();

        $url = route('api.menus.relationships.category.update', $menu);

        $response = $this->patchJson($url, [
            'data' => [
                'type' => 'categories',
                'id' => (string)$category->getRouteKey(),
            ]
        ]);

        $response->assertExactJson([
            'data' => [
                'type' => 'categories',
                'id' => (string)$category->getRouteKey()
            ]
        ]);

        $this->assertDatabaseHas('menus', [
            'id' => $menu->id,
            'category_id' => $category->id
        ]);
    }

    #[Test]
    public function category_must_exist_in_database(): void
    {
        $this->authenticateUser(['menu:update']);

        $menu = Menu::factory()->create();
        $url = route('api.menus.relationships.category.update', $menu);

        $this->patchJson($url, [
            'data' => [
                'type' => 'categories',
                'id' => 'no-existing',
            ]
        ])->assertJsonApiValidationErrors('data.id');


        $this->assertDatabaseHas('menus', [
            'id' => $menu->id,
            'category_id' => $menu->category_id
        ]);
    }
}

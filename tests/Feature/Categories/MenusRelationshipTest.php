<?php

namespace Tests\Feature\Categories;

use App\Models\Category;
use App\Models\Menu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\AuthUser;

class MenusRelationshipTest extends TestCase
{
    use RefreshDatabase, AuthUser;

    #[Test]
    public function can_fetch_the_associated_menus_resources(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['category:show']);

        $category = Category::factory()->create();
        $menus = Menu::factory()->count(3)->create(['category_id' => $category->id]);

        $response = $this->getJson(route('api.categories.menus', $category));
        $response->assertJsonApiResourceCollection($menus, [
            'name',
            'description',
            'price',
            'photo_url'
        ]);
    }


    #[Test]
    public function can_fetch_menu_relationships(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['category:show']);

        $category = Category::factory()->create();
        $menu1 = Menu::factory()->create(['category_id' => $category->id]);
        $menu2 = Menu::factory()->create(['category_id' => $category->id]);
        Menu::factory()->create();

        $response = $this->getJson(route('api.categories.relationships.menus.show', $category));
        $response->assertExactJson([
            'data' => [
                [
                    'id' => (string)$menu1->getRouteKey(),
                    'type' => 'menus',
                ],
                [
                    'id' => (string)$menu2->getRouteKey(),
                    'type' => 'menus',
                ],
            ]
        ]);
    }
}

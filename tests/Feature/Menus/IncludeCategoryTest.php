<?php

namespace Tests\Feature\Menus;

use App\Models\Menu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\AuthUser;

class IncludeCategoryTest extends TestCase
{
    use RefreshDatabase, AuthUser;

    #[Test]
    public function can_include_related_category_of_an_menu(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['menu:show']);

        $menu = Menu::factory()->create();

        $url = route('api.menus.show', [
            'menu' => $menu,
            'include' => 'category'
        ]);

        $this->getJson($url)
            ->assertJson([
                'data' => [],
                'included' => [
                    [
                        'type' => 'categories',
                        'id' => $menu->category->getRouteKey(),
                        'attributes' => [
                            'name' => $menu->category->name
                        ]
                    ]
                ]
            ]);
    }

    #[Test]
    public function can_include_related_categories_of_multiple_menus(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['menu:index']);

        $menu1 = Menu::factory()->create()->load('category');
        $menu2 = Menu::factory()->create()->load('category');

        $url = route('api.menus.index', [
            'include' => 'category'
        ]);

        $this->getJson($url)
            ->assertJson([
                'data' => [],
                'included' => [
                    [
                        'type' => 'categories',
                        'id' => $menu1->category->getRouteKey(),
                        'attributes' => [
                            'name' => $menu1->category->name
                        ]
                    ],
                    [
                        'type' => 'categories',
                        'id' => $menu2->category->getRouteKey(),
                        'attributes' => [
                            'name' => $menu2->category->name
                        ]
                    ]
                ]
            ]);
    }

    #[Test]
    public function cannot_include_unknown_relationships(): void
    {
        $this->authenticateUser(['menu:show','menu:index']);

        $menu = Menu::factory()->create();

        $url = route('api.menus.show', [
            'menu' => $menu,
            'include' => 'unknown,unknown2'
        ]);

        $this->getJson($url)
            ->assertJsonApiError(
                title: trans('httpCodes.400'),
                detail: "The included relationship 'unknown' is not allowed in the 'menus' resource.",
                status: "400",
            );

        $url = route('api.menus.index', [
            'include' => 'unknown,unknown2'
        ]);

        $this->getJson($url)->assertJsonApiError(
            title: trans('httpCodes.400'),
            detail: "The included relationship 'unknown' is not allowed in the 'menus' resource.",
            status: "400",
        );
    }
}

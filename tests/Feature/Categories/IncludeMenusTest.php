<?php

namespace Tests\Feature\Categories;

use App\Models\Category;
use App\Models\Menu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\AuthUser;

class IncludeMenusTest extends TestCase
{
    use RefreshDatabase, AuthUser;

    #[Test]
    public function can_includes_related_menus_when_showing_a_category(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['category:show']);

        $category = Category::factory()->create();
        Menu::factory(2)->create(['category_id' => $category->id]);
        Menu::factory()->create();

        $url = route('api.categories.show', [
            'category' => $category,
            'include' => 'menus'
        ]);

        $this->getJson($url)
            ->assertJson([
                'data' => [],
                'included' => [
                    [
                        'type' => 'menus',
                        'id' => $category->menus[0]->getRouteKey(),
                        'attributes' => [
                            'name' => $category->menus[0]->name
                        ]
                    ],
                    [
                        'type' => 'menus',
                        'id' => $category->menus[1]->getRouteKey(),
                        'attributes' => [
                            'name' => $category->menus[1]->name
                        ]
                    ]
                ]
            ]);
    }

    #[Test]
    public function can_includes_related_menus_for_multiple_categories(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['category:index']);

        $category = Category::factory()->create();
        Menu::factory(2)->create(['category_id' => $category->id]);
        $category1 = Category::factory()->create();
        Menu::factory(1)->create(['category_id' => $category1->id]);

        $url = route('api.categories.index', [
            'include' => 'menus'
        ]);

        $this->getJson($url)
            ->assertJson([
                'data' => [],
                'included' => [
                    [
                        'type' => 'menus',
                        'id' => $category->menus[0]->getRouteKey(),
                        'attributes' => [
                            'name' => $category->menus[0]->name
                        ]
                    ],
                    [
                        'type' => 'menus',
                        'id' => $category->menus[1]->getRouteKey(),
                        'attributes' => [
                            'name' => $category->menus[1]->name
                        ]
                    ],
                    [
                        'type' => 'menus',
                        'id' => $category1->menus[0]->getRouteKey(),
                        'attributes' => [
                            'name' => $category1->menus[0]->name
                        ]
                    ],
                ]
            ]);
    }

    #[Test]
    public function cannot_include_unknown_relationships(): void
    {
        $category = Category::factory()->create();
        $this->authenticateUser(['category:show','category:index']);

        $url = route('api.categories.show', [
            'category' => $category,
            'include' => 'unknown,menus'
        ]);

        $this->getJson($url)->assertJsonApiError(
                title: trans('httpCodes.400'),
                detail: "The included relationship 'unknown' is not allowed in the 'categories' resource.",
                status: "400",
            );

        $url = route('api.categories.index', [
            'include' => 'unknown,unknown2'
        ]);

        $this->getJson($url)->assertJsonApiError(
            title: trans('httpCodes.400'),
            detail: "The included relationship 'unknown' is not allowed in the 'categories' resource.",
            status: "400",
        );
    }
}

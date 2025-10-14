<?php

namespace Tests\Feature\Menus;

use App\Models\Category;
use App\Models\Menu;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\AuthUser;

class UpdateMenuTest extends TestCase
{
    use RefreshDatabase, AuthUser;

    #[Test]
    public function can_update_menus(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['menu:update']);

        $menu = Menu::factory()->create();
        $new_category = Category::factory()->create();

        $response = $this->patchJson(route('api.menus.update', $menu), [
            'name' => 'test to update a menu',
            'slug' => $menu->slug,
            'description' => 'test description menu',
            'price' => 1.55,
            'stock' => 0,
            'priority' =>  'H',
            'enabled' => true,
            '_relationships' => [
                'category' => $new_category
            ]
        ]);

        $response->assertOk();

        $response->assertJsonApiResource($menu, [
            'name' => 'test to update a menu',
            'slug' => $menu->slug,
            'description' => 'test description menu',
            'price' => 1.55,
            'photo_url' => null,
            'stock' => 0,
            'priority' =>  'H',
            'enabled' => true,
        ]);

        $this->assertDatabaseHas('menus', ['category_id' => $new_category->id]);
    }

    #[Test]
    public function name_is_required()
    {
        $this->authenticateUser(['menu:update']);
        $menu = Menu::factory()->create();
        $this->patchJson(route('api.menus.update', $menu),  [
            'description' => 'test description menu',
            'priority' =>  '0'
        ])->assertJsonApiValidationErrors('name');
    }

    #[Test]
    public function slug_is_required()
    {
        $this->authenticateUser(['menu:update']);
        $menu = Menu::factory()->create();
        $this->patchJson(route('api.menus.update', $menu),  [
            'name' => 'test name menu',
            'description' => 'test description menu',
            'priority' =>  '0'
        ])->assertJsonApiValidationErrors('slug');
    }

    #[Test]
    public function slug_must_be_format_valid()
    {
        $this->authenticateUser(['menu:update']);
        $menu = Menu::factory()->create();
        $this->patchJson(route('api.menus.update', $menu),  [
            'name' => 'test name menu',
            'slug' => $menu->slug . '-',
            'description' => 'test description menu',
            'priority' =>  '0'
        ])->assertJsonApiValidationErrors('slug');
    }

    #[Test]
    public function slug_must_be_unique()
    {
        $this->authenticateUser(['menu:update']);
        $menu = Menu::factory()->create();
        $menu1 = Menu::factory()->create();

        $this->patchJson(route('api.menus.update', $menu),  [
            'name' => 'test name menu',
            'slug' => $menu1->slug,
            'description' => 'test description menu',
            'priority' =>  '0'
        ])->assertJsonApiValidationErrors('slug');
    }

    #[Test]
    public function description_is_required()
    {
        $this->authenticateUser(['menu:update']);
        $menu = Menu::factory()->create();
        $this->patchJson(route('api.menus.update', $menu),  [
            'name' => 'test to update a menu',
            'priority' =>  '0'
        ])->assertJsonApiValidationErrors('description');
    }

    #[Test]
    public function priority_is_required()
    {
        $this->authenticateUser(['menu:update']);
        $menu = Menu::factory()->create();
        $this->patchJson(route('api.menus.update', $menu),  [
            'name' => 'test to update a menu',
            'description' => 'test description menu'
        ])->assertJsonApiValidationErrors('priority');
    }

    #[Test]
    public function priority_invalid_value()
    {
        $this->authenticateUser(['menu:update']);
        $menu = Menu::factory()->create();
        $this->patchJson(route('api.menus.update', $menu),  [
            'name' => 'test to update a menu',
            'description' => 'test description menu',
            'priority' => '2132',
        ])->assertJsonApiValidationErrors('priority');
    }

    #[Test]
    public function guests_cannot_update_menus(): void
    {
        $menu = Menu::factory()->create();

        $this->patchJson(route('api.menus.update', $menu))
            ->assertJsonApiError(
                title: trans('httpCodes.401'),
                detail: trans('myMessages.ActionRequiresAuth'),
                status: '401'
            );
    }
}

<?php

namespace Tests\Feature\Menus;

use App\Models\Category;
use App\Models\Menu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\AuthUser;

class CreateMenuTest extends TestCase
{
    use RefreshDatabase, AuthUser;

    #[Test]
    public function can_create_menus(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['menu:create']);

        $category = Category::factory()->create();

        $response = $this->postJson(route('api.menus.store'), [
            'name' => 'test to create a menu',
            'slug' => 'test-slug',
            'description' => 'test description menu',
            'price' => 1.55,
            'stock' => 0,
            'priority' =>  'H',
            'enabled' => true,
            '_relationships' => [
                'category' => $category
            ]
        ]);
        $response->assertStatus(201);

        $menu = Menu::first();

        $response->assertJsonApiResource($menu, [
            'name' => 'test to create a menu',
            'slug' => 'test-slug',
            'description' => 'test description menu',
            'price' => 1.55,
            'photo_url' => $menu->photo_url,
            'stock' => 0,
            'priority' =>  'H',
            'enabled' => true,
        ]);
    }

    #[Test]
    public function name_is_required()
    {
        $this->authenticateUser(['menu:create']);
        $this->postJson(route('api.menus.store'),  [
            'description' => 'test description menu',
            'priority' =>  '0'
        ])->assertJsonApiValidationErrors('name');
    }

    #[Test]
    public function slug_is_required()
    {
        $this->authenticateUser(['menu:create']);
        $this->postJson(route('api.menus.store'),  [
            'name' => 'test name menu',
            'description' => 'test description menu',
            'priority' =>  '0'
        ])->assertJsonApiValidationErrors('slug');
    }

    #[Test]
    public function slug_must_be_format_valid()
    {
        $this->authenticateUser(['menu:create']);
        $this->postJson(route('api.menus.store'),  [
            'name' => 'test name menu',
            'slug' => '%&$^$',
            'description' => 'test description menu',
            'priority' =>  '0'
        ])->assertJsonApiValidationErrors('slug');
    }

    #[Test]
    public function slug_must_be_unique()
    {
        $this->authenticateUser(['menu:create']);
        $menu = Menu::factory()->create();
        $this->postJson(route('api.menus.store'),  [
            'name' => 'test name menu',
            'slug' => $menu->slug,
            'description' => 'test description menu',
            'priority' =>  '0'
        ])->assertJsonApiValidationErrors('slug');
    }

    #[Test]
    public function description_is_required()
    {
        $this->authenticateUser(['menu:create']);
        $this->postJson(route('api.menus.store'),  [
            'name' => 'test to create a menu',
            'priority' =>  '0'
        ])->assertJsonApiValidationErrors('description');
    }

    #[Test]
    public function priority_is_required()
    {
        $this->authenticateUser(['menu:create']);
        $this->postJson(route('api.menus.store'),  [
            'name' => 'test to create a menu',
            'description' => 'test description menu'
        ])->assertJsonApiValidationErrors('priority');
    }

    #[Test]
    public function priority_invalid_value()
    {
        $this->authenticateUser(['menu:create']);
        $this->postJson(route('api.menus.store'),  [
            'name' => 'test to create a menu',
            'description' => 'test description menu',
            'priority' => '4587',
        ])->assertJsonApiValidationErrors('priority');
    }


    #[Test]
    public function category_relationship_is_required()
    {
        $this->authenticateUser(['menu:create']);
        $this->postJson(route('api.menus.store'),  [
            'name' => 'test to create a menu with category is required',
            'description' => 'test description menu'
        ])->assertJsonApiValidationErrors('data.relationships.category.data.id');
    }

    #[Test]
    public function category_must_exist_in_database()
    {
        $this->authenticateUser(['menu:create']);
        $this->postJson(route('api.menus.store'),  [
            'name' => 'test to create a menu with category exist',
            'slug' => 'test-slug',
            'description' => 'test description menu',
            '_relationships' => [
                'category' => Category::factory()->make()
            ]
        ])->assertJsonApiValidationErrors('data.relationships.category.data.id');
    }


    #[Test]
    public function guests_cannot_create_menus()
    {
        $this->postJson(route('api.menus.store'))
            ->assertJsonApiError(
                title: trans('httpCodes.401'),
                detail: trans('myMessages.ActionRequiresAuth'),
                status: '401'
            );

        $this->assertDatabaseCount('menus', 0);
    }
}

<?php

namespace Tests\Feature\Menus;

use App\Models\Category;
use App\Models\Menu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\AuthUser;

class SparseFieldsMenusTest extends TestCase
{
    use RefreshDatabase, AuthUser;

    #[Test]
    public function specific_fields_can_be_requested_in_the_menus_index(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['menu:index']);

        $menu = Menu::factory()->create();

        $url = route('api.menus.index', [
            'fields' => [
                'menus' => 'name,slug'
            ]
        ]);

        $this->getJson($url)
            ->assertJsonFragment([
                'name' => $menu->name,
                'slug' => $menu->slug
            ])->assertJsonMissing([
                'description' => $menu->description,
                'priority' => $menu->priority
            ])->assertJsonMissing([
                'description' => null,
                'priority' => null
            ]);
    }

    #[Test]
    public function route_key_must_be_added_automatically_in_the_menus_index(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['menu:index']);

        $menu = Menu::factory()->create();

        $url = route('api.menus.index', [
            'fields' => [
                'menus' => 'name'
            ]
        ]);

        $this->getJson($url)
            ->assertJsonFragment([
                'name' => $menu->name
            ])->assertJsonMissing([
                'slug' => $menu->slug,
                'description' => $menu->description,
                'priority' => $menu->priority
            ]);
    }

    #[Test]
    public function specific_fields_can_be_requested_in_the_menus_show(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['menu:show']);

        $menu = Menu::factory()->create();

        $url = route('api.menus.show', [
            'menu' => $menu,
            'fields' => [
                'menus' => 'name,slug'
            ]
        ]);

        $this->getJson($url)
            ->assertJsonFragment([
                'name' => $menu->name,
                'slug' => $menu->slug
            ])->assertJsonMissing([
                'description' => $menu->description,
                'priority' => $menu->priority
            ])->assertJsonMissing([
                'description' => null,
                'priority' => null
            ]);
    }

    #[Test]
    public function route_key_must_be_added_automatically_in_the_menus_show(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['menu:show']);

        $menu = Menu::factory()->create();

        $url = route('api.menus.show', [
            'menu' => $menu,
            'fields' => [
                'menus' => 'name'
            ]
        ]);

        $this->getJson($url)
            ->assertJsonFragment([
                'name' => $menu->name
            ])->assertJsonMissing([
                'slug' => $menu->slug,
                'description' => $menu->description,
                'priority' => $menu->priority
            ]);
    }
}

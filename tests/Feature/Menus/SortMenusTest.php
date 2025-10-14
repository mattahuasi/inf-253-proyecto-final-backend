<?php

namespace Tests\Feature\Menus;

use App\Models\Menu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\AuthUser;

class SortMenusTest extends TestCase
{
    use RefreshDatabase, AuthUser;

    #[Test]
    public function can_sort_menus_by_name(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['menu:index']);

        Menu::factory()->create(['name' => 'C name']);
        Menu::factory()->create(['name' => 'B name']);
        Menu::factory()->create(['name' => 'A name']);

        $url = route('api.menus.index', ['sort' => 'name']);

        $this->getJson($url)->assertSeeInOrder([
            'A name',
            'B name',
            'C name',
        ]);
    }

    #[Test]
    public function can_sort_menus_by_name_desc(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['menu:index']);

        Menu::factory()->create(['name' => 'B name']);
        Menu::factory()->create(['name' => 'C name']);
        Menu::factory()->create(['name' => 'A name']);

        $url = route('api.menus.index', ['sort' => '-name']);

        $this->getJson($url)->assertSeeInOrder([
            'C name',
            'B name',
            'A name',
        ]);
    }

    #[Test]
    public function can_sort_menus_by_description(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['menu:index']);

        Menu::factory()->create(['description' => 'C description']);
        Menu::factory()->create(['description' => 'B description']);
        Menu::factory()->create(['description' => 'A description']);

        $url = route('api.menus.index', ['sort' => 'description']);

        $this->getJson($url)->assertSeeInOrder([
            'A description',
            'B description',
            'C description',
        ]);
    }

    #[Test]
    public function can_sort_menus_by_description_desc(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['menu:index']);

        Menu::factory()->create(['description' => 'B description']);
        Menu::factory()->create(['description' => 'C description']);
        Menu::factory()->create(['description' => 'A description']);

        $url = route('api.menus.index', ['sort' => '-description']);

        $this->getJson($url)->assertSeeInOrder([
            'C description',
            'B description',
            'A description',
        ]);
    }

    #[Test]
    public function can_sort_menus_by_priority(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['menu:index']);

        Menu::factory()->create(['priority' => 'M']);
        Menu::factory()->create(['priority' => 'L']);
        Menu::factory()->create(['priority' => 'H']);

        $url = route('api.menus.index', ['sort' => 'priority']);

        $this->getJson($url)
            ->assertSeeInOrder([
                'H',
                'M',
                'L',
            ]);
    }

    #[Test]
    public function can_sort_menus_by_priority_desc(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['menu:index']);

        Menu::factory()->create(['priority' => 'M']);
        Menu::factory()->create(['priority' => 'L']);
        Menu::factory()->create(['priority' => 'H']);

        $url = route('api.menus.index', ['sort' => '-priority']);

        $this->getJson($url)->assertSeeInOrder([
            'L',
            'M',
            'H',
        ]);
    }

    #[Test]
    public function can_sort_menus_by_asc_name_and_desc_description(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['menu:index']);

        Menu::factory()->create([
            'name' => 'C name',
            'description' => 'D description',
        ]);
        Menu::factory()->create([
            'name' => 'A name',
            'description' => 'A description',
        ]);
        Menu::factory()->create([
            'name' => 'A name',
            'description' => 'C description',
        ]);

        $url = route('api.menus.index', ['sort' => 'name,description']);

        $this->getJson($url)
            ->assertSeeInOrder([
                'A description',
                'C description',
                'D description',
            ]);
    }

    #[Test]
    public function can_sort_menus_by_unknown_fields(): void
    {
        $this->authenticateUser(['menu:index']);
        Menu::factory()->create();
        Menu::factory()->create();
        Menu::factory()->create();

        $url = route('api.menus.index', ['sort' => 'unknown']);

        $this->getJson($url)
            ->assertJsonApiError(
                title: trans('httpCodes.400'),
                detail: "The sort field 'unknown' is not allowed in the 'menus' resource.",
                status: "400",
            );
    }
}

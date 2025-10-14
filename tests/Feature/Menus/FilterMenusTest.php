<?php

namespace Tests\Feature\Menus;

use App\Models\Category;
use App\Models\Menu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\AuthUser;

class FilterMenusTest extends TestCase
{
    use RefreshDatabase, AuthUser;

    #[Test]
    public function can_filter_menus_by_name(): void
    {
        $this->authenticateUser(['menu:index']);

        Menu::factory()->create(['name' => 'C name']);
        Menu::factory()->create(['name' => 'B name test']);
        Menu::factory()->create(['name' => 'A name']);


        $url = route('api.menus.index', [
            'filter' => [
                'name' => 'test'
            ]
        ]);

        $this->getJson($url)
            ->assertJsonCount(1, 'data')
            ->assertSee('B name test')
            ->assertDontSee([
                'C name',
                'A name'
            ]);
    }

    #[Test]
    public function can_filter_menus_by_priority(): void
    {
        $this->authenticateUser(['menu:index']);

        Menu::factory()->create(['priority' => 'H']);
        Menu::factory()->create(['priority' => 'L']);
        Menu::factory()->create(['priority' => 'H']);

        $url = route('api.menus.index', [
            'filter' => [
                'priority' => 'H'
            ]
        ]);

        $this->getJson($url)
            ->assertJsonCount(2, 'data')
            ->assertSee([
                'H',
                'H'
            ]);
    }


    #[Test]
    public function can_filter_menus_by_category(): void
    {
        $this->authenticateUser(['menu:index']);

        Menu::factory(2)->create();
        $cat1 = Category::factory()->hasMenus(2)->create(['slug' => 'cat-1']);
        $cat2 = Category::factory()->hasMenus(1)->create(['slug' => 'cat-2']);

        //menus?filter[categories]=cat-1
        $url = route('api.menus.index', [
            'filter' => [
                'categories' => 'cat-1,cat-2'
            ]
        ]);

        $this->getJson($url)
        ->assertJsonCount(3, 'data')
            ->assertSee($cat1->menus[0]->slug)
            ->assertSee($cat1->menus[1]->slug)
            ->assertSee($cat2->menus[0]->slug);
    }


    #[Test]
    public function cannot_filter_menus_by_unknown(): void
    {
        $this->authenticateUser(['menu:index']);

        Menu::factory(5)->create();

        $url = route('api.menus.index', [
            'filter' => [
                'unknown' => '-'
            ]
        ]);

        $this->getJson($url)
            ->assertJsonApiError(
                title: trans('httpCodes.400'),
                detail: "The filter field 'unknown' is not allowed in the 'menus' resource.",
                status: "400",
            );
    }
}

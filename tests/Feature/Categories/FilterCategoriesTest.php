<?php

namespace Tests\Feature\Categories;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\AuthUser;

class FilterCategoriesTest extends TestCase
{
    use RefreshDatabase, AuthUser;

    #[Test]
    public function can_filter_categories_by_name(): void
    {
        $this->authenticateUser(['category:index']);
        Category::factory()->create(['name' => 'C name']);
        Category::factory()->create(['name' => 'B name test']);
        Category::factory()->create(['name' => 'A name']);

        //categories?filter[title]=test

        $url = route('api.categories.index', [
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
    public function can_filter_categories_by_priority(): void
    {
        $this->authenticateUser(['category:index']);
        Category::factory()->create(['priority' => '0']);
        Category::factory()->create(['priority' => '8']);
        Category::factory()->create(['priority' => '0']);

        $url = route('api.categories.index', [
            'filter' => [
                'priority' => '0'
            ]
        ]);

        $this->getJson($url)
            ->assertJsonCount(2, 'data')
            ->assertSee([
                '0',
                '0'
            ]);
    }

    #[Test]
    public function cannot_filter_categories_by_unknown(): void
    {
        $this->authenticateUser(['category:index']);
        Category::factory(5)->create();

        $url = route('api.categories.index', [
            'filter' => [
                'unknown' => '-'
            ]
        ]);

        $this->getJson($url)->assertStatus(400);
    }
}

<?php

namespace Tests\Feature\Categories;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\AuthUser;

class SortCategoriesTest extends TestCase
{
    use RefreshDatabase, AuthUser;

    #[Test]
    public function can_sort_categories_by_name(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['category:index']);

        Category::factory()->create(['name' => 'C name']);
        Category::factory()->create(['name' => 'B name']);
        Category::factory()->create(['name' => 'A name']);

        $url = route('api.categories.index', ['sort' => 'name']);

        $this->getJson($url)->assertSeeInOrder([
            'A name',
            'B name',
            'C name',
        ]);
    }

    #[Test]
    public function can_sort_categories_by_name_desc(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['category:index']);

        Category::factory()->create(['name' => 'B name']);
        Category::factory()->create(['name' => 'C name']);
        Category::factory()->create(['name' => 'A name']);

        $url = route('api.categories.index', ['sort' => '-name']);

        $this->getJson($url)->assertSeeInOrder([
            'C name',
            'B name',
            'A name',
        ]);
    }

    #[Test]
    public function can_sort_categories_by_description(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['category:index']);

        Category::factory()->create(['description' => 'C description']);
        Category::factory()->create(['description' => 'B description']);
        Category::factory()->create(['description' => 'A description']);

        $url = route('api.categories.index', ['sort' => 'description']);

        $this->getJson($url)->assertSeeInOrder([
            'A description',
            'B description',
            'C description',
        ]);
    }

    #[Test]
    public function can_sort_categories_by_description_desc(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['category:index']);

        Category::factory()->create(['description' => 'B description']);
        Category::factory()->create(['description' => 'C description']);
        Category::factory()->create(['description' => 'A description']);

        $url = route('api.categories.index', ['sort' => '-description']);

        $this->getJson($url)->assertSeeInOrder([
            'C description',
            'B description',
            'A description',
        ]);
    }


    #[Test]
    public function can_sort_categories_by_priority(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['category:index']);

        Category::factory()->create(['priority' => '9']);
        Category::factory()->create(['priority' => '0']);
        Category::factory()->create(['priority' => '2']);

        $url = route('api.categories.index', ['sort' => 'priority']);

        $this->getJson($url)->assertSeeInOrder([
            '0',
            '2',
            '9',
        ]);
    }

    #[Test]
    public function can_sort_categories_by_priority_desc(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['category:index']);

        Category::factory()->create(['priority' => '9']);
        Category::factory()->create(['priority' => '0']);
        Category::factory()->create(['priority' => '2']);

        $url = route('api.categories.index', ['sort' => '-priority']);

        $this->getJson($url)->assertSeeInOrder([
            '9',
            '2',
            '0',
        ]);
    }


    #[Test]
    public function can_sort_categories_by_asc_name_and_desc_description(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['category:index']);

        Category::factory()->create([
            'name' => 'C name',
            'description' => 'D description',
            // 'priority' => '3
        ]);
        Category::factory()->create([
            'name' => 'A name',
            'description' => 'A description',
            // 'priority' => '0'
        ]);
        Category::factory()->create([
            'name' => 'A name',
            'description' => 'C description',
            // 'priority' => '1'
        ]);

        $url = route('api.categories.index', ['sort' => 'name,description']);

        $this->getJson($url)
            ->assertSeeInOrder([
                'A description',
                'C description',
                'D description',
            ]);
    }

    #[Test]
    public function can_sort_categories_by_unknown_fields(): void
    {
        $this->authenticateUser(['category:index']);

        Category::factory()->create();
        Category::factory()->create();
        Category::factory()->create();

        $url = route('api.categories.index', ['sort' => 'unknown']);

        $this->getJson($url)
            ->assertStatus(400);
    }
}

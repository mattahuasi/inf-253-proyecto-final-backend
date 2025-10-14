<?php

namespace Tests\Feature\Categories;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\AuthUser;

class SparseFieldsCategoriesTest extends TestCase
{
    use RefreshDatabase, AuthUser;

    #[Test]
    public function specific_fields_can_be_requested_in_the_categories_index(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['category:index']);

        $category = Category::factory()->create();

        $url = route('api.categories.index', [
            'fields' => [
                'categories' => 'name,slug'
            ]
        ]);

        $this->getJson($url)
            ->assertJsonFragment([
                'name' => $category->name,
                'slug' => $category->slug
            ])->assertJsonMissing([
                'description' => $category->description,
                'priority' => $category->priority
            ])->assertJsonMissing([
                'description' => null,
                'priority' => null
            ]);
    }

    #[Test]
    public function route_key_must_be_added_automatically_in_the_categories_index(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['category:index']);

        $category = Category::factory()->create();

        $url = route('api.categories.index', [
            'fields' => [
                'categories' => 'name'
            ]
        ]);

        $this->getJson($url)
            ->assertJsonFragment([
                'name' => $category->name
            ])->assertJsonMissing([
                'slug' => $category->slug,
                'description' => $category->description,
                'priority' => $category->priority
            ]);
    }

    #[Test]
    public function specific_fields_can_be_requested_in_the_categories_show(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['category:show']);

        $category = Category::factory()->create();

        $url = route('api.categories.show', [
            'category' => $category,
            'fields' => [
                'categories' => 'name,slug'
            ]
        ]);

        $this->getJson($url)
            ->assertJsonFragment([
                'name' => $category->name,
                'slug' => $category->slug
            ])->assertJsonMissing([
                'description' => $category->description,
                'priority' => $category->priority
            ])->assertJsonMissing([
                'description' => null,
                'priority' => null
            ]);
    }

    #[Test]
    public function route_key_must_be_added_automatically_in_the_categories_show(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['category:show']);

        $category = Category::factory()->create();

        $url = route('api.categories.show', [
            'category' => $category,
            'fields' => [
                'categories' => 'name'
            ]
        ]);

        $this->getJson($url)
            ->assertJsonFragment([
                'name' => $category->name
            ])->assertJsonMissing([
                'slug' => $category->slug,
                'description' => $category->description,
                'priority' => $category->priority
            ]);
    }
}

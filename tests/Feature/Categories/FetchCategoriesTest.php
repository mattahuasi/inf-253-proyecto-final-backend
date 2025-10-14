<?php

namespace Tests\Feature\Categories;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\AuthUser;

class FetchCategoriesTest extends TestCase
{
    use RefreshDatabase, AuthUser;

    #[Test]
    public function can_fetch_a_single_category(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['category:show']);

        $category = Category::factory()->create();

        $response = $this->getJson(route('api.categories.show', $category));

        $response->assertJsonApiResource($category, [
            'name' => $category->name,
            'slug' => $category->slug,
            'description' => $category->description,
            'priority' => $category->priority
        ]);
    }

    #[Test]
    public function can_fetch_all_categories(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['category:index']);

        $categories = Category::factory()->count(3)->create();

        $response = $this->getJson(route('api.categories.index'));

        $response->assertJsonApiResourceCollection($categories, [
            'name',
            'slug',
            'description',
            'priority'
        ]);
    }
}

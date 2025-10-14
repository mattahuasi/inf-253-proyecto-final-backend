<?php

namespace Tests\Feature\Categories;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\AuthUser;

class UpdateCategoryTest extends TestCase
{
    use RefreshDatabase, AuthUser;

    #[Test]
    public function can_update_categories(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['category:update']);

        $category = Category::factory()->create();

        $response = $this->patchJson(route('api.categories.update', $category), [
            'name' => 'test update name category',
            'slug' => $category->slug,
            'description' => 'test update description category',
            'priority' => '0'
        ]);

        $response->assertOk();

        $response->assertJsonApiResource($category, [
            'name' => 'test update name category',
            'slug' => $category->slug,
            'description' => 'test update description category',
            'priority' => '0'
        ]);
    }

    #[Test]
    public function name_is_required()
    {
        $this->authenticateUser(['category:update']);
        $category = Category::factory()->create();
        $this->patchJson(route('api.categories.update', $category),  [
            'description' => 'test description category',
            'priority' =>  '0'
        ])->assertJsonApiValidationErrors('name');
    }

    #[Test]
    public function slug_is_required()
    {
        $this->authenticateUser(['category:update']);
        $category = Category::factory()->create();
        $this->patchJson(route('api.categories.update', $category),  [
            'name' => 'test name category',
            'description' => 'test description category',
            'priority' =>  '0'
        ])->assertJsonApiValidationErrors('slug');
    }

    #[Test]
    public function slug_must_be_format_valid()
    {
        $this->authenticateUser(['category:update']);
        $category = Category::factory()->create();
        $this->patchJson(route('api.categories.update', $category),  [
            'name' => 'test name category',
            'slug' => $category->slug . '-',
            'description' => 'test description category',
            'priority' =>  '0'
        ])->assertJsonApiValidationErrors('slug');
    }

    #[Test]
    public function slug_must_be_unique()
    {
        $this->authenticateUser(['category:update']);
        $category = Category::factory()->create();
        $category1 = Category::factory()->create();

        $this->patchJson(route('api.categories.update', $category),  [
            'name' => 'test name category',
            'slug' => $category1->slug,
            'description' => 'test description category',
            'priority' =>  '0'
        ])->assertJsonApiValidationErrors('slug');
    }

    #[Test]
    public function description_is_required()
    {
        $this->authenticateUser(['category:update']);
        $category = Category::factory()->create();
        $this->patchJson(route('api.categories.update', $category),  [
            'name' => 'test to update a category',
            'priority' =>  '0'
        ])->assertJsonApiValidationErrors('description');
    }

    #[Test]
    public function priority_is_required()
    {
        $this->authenticateUser(['category:update']);
        $category = Category::factory()->create();
        $this->patchJson(route('api.categories.update', $category),  [
            'name' => 'test to update a category',
            'description' => 'test description category'
        ])->assertJsonApiValidationErrors('priority');
    }

    #[Test]
    public function priority_invalid_value()
    {
        $this->authenticateUser(['category:update']);
        $category = Category::factory()->create();
        $this->patchJson(route('api.categories.update', $category),  [
            'name' => 'test to update a category',
            'description' => 'test description category',
            'priority' => '2132',
        ])->assertJsonApiValidationErrors('priority');
    }
}

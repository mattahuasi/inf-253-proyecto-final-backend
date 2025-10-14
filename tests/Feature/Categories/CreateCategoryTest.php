<?php

namespace Tests\Feature\Categories;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\AuthUser;

class CreateCategoryTest extends TestCase
{
    use RefreshDatabase, AuthUser;

    #[Test]
    public function can_create_categories(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['category:create']);

        $response = $this->postJson(route('api.categories.store'), [
            'name' => 'test to create a category',
            'slug' => 'test-slug',
            'description' => 'test description category',
            'priority' =>  '0'
        ]);
        $response->assertStatus(201);

        $category = Category::first();

        $response->assertJsonApiResource($category, [
            'name' => 'test to create a category',
            'slug' => 'test-slug',
            'description' => 'test description category',
            'priority' =>  '0'
        ]);
    }

    #[Test]
    public function name_is_required()
    {
        $this->authenticateUser(['category:create']);
        $this->postJson(route('api.categories.store'),  [
            'description' => 'test description category',
            'priority' =>  '0'
        ])->assertJsonApiValidationErrors('name');
    }

    #[Test]
    public function slug_is_required()
    {
        $this->authenticateUser(['category:create']);
        $this->postJson(route('api.categories.store'),  [
            'name' => 'test name category',
            'description' => 'test description category',
            'priority' =>  '0'
        ])->assertJsonApiValidationErrors('slug');
    }

    #[Test]
    public function slug_must_be_format_valid()
    {
        $this->authenticateUser(['category:create']);
        $this->postJson(route('api.categories.store'),  [
            'name' => 'test name category',
            'slug' => '%&$^$',
            'description' => 'test description category',
            'priority' =>  '0'
        ])->assertJsonApiValidationErrors('slug');
    }

    #[Test]
    public function slug_must_be_unique()
    {
        $this->authenticateUser(['category:create']);
        $category = Category::factory()->create();

        $this->postJson(route('api.categories.store'),  [
            'name' => 'test name category',
            'slug' => $category->slug,
            'description' => 'test description category',
            'priority' =>  '0'
        ])->assertJsonApiValidationErrors('slug');
    }

    #[Test]
    public function description_is_required()
    {
        $this->authenticateUser(['category:create']);
        $this->postJson(route('api.categories.store'),  [
            'name' => 'test to create a category',
            'priority' =>  '0'
        ])->assertJsonApiValidationErrors('description');
    }

    #[Test]
    public function priority_is_required()
    {
        $this->authenticateUser(['category:create']);
        $this->postJson(route('api.categories.store'),  [
            'name' => 'test to create a category',
            'description' => 'test description category'
        ])->assertJsonApiValidationErrors('priority');
    }

    #[Test]
    public function priority_invalid_value()
    {
        $this->authenticateUser(['category:create']);
        $this->postJson(route('api.categories.store'),  [
            'name' => 'test to create a category',
            'description' => 'test description category',
            'priority' => '4587',
        ])->assertJsonApiValidationErrors('priority');
    }
}

<?php

namespace Tests\Feature\Categories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\AuthUser;

class DeleteCategoryTest extends TestCase
{
    use RefreshDatabase, AuthUser;

    #[Test]
    public function can_delete_categories(): void
    {
        $this->authenticateUser(['category:delete']);;
        $category = Category::factory()->create();
        $this->deleteJson(route('api.categories.destroy', $category))
            ->assertNoContent();
    }

    #[Test]
    public function guests_cannot_delete_categories(): void
    {
        $category = Category::factory()->create();
        $this->deleteJson(route('api.categories.destroy', $category))
            ->assertJsonApiError(
                title: trans('httpCodes.401'),
                detail: trans('myMessages.ActionRequiresAuth'),
                status: '401'
            );
    }
}

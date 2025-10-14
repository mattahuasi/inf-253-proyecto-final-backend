<?php

namespace Tests\Feature\Menus;

use App\Models\Menu;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\AuthUser;

class DeleteMenuTest extends TestCase
{
    use RefreshDatabase, AuthUser;

    #[Test]
    public function can_delete_menus(): void
    {
        $this->authenticateUser(['menu:delete']);
        $menu = Menu::factory()->create();
        $this->deleteJson(route('api.menus.destroy', $menu))
            ->assertNoContent();
    }

    #[Test]
    public function guests_cannot_delete_menus(): void
    {
        $menu = Menu::factory()->create();
        $this->deleteJson(route('api.menus.destroy', $menu))
            ->assertJsonApiError(
                title: trans('httpCodes.401'),
                detail: trans('myMessages.ActionRequiresAuth'),
                status: '401'
            );
    }
}

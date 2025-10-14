<?php

namespace Tests\Feature\States;

use App\Models\State;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\AuthUser;

class DeleteStateTest extends TestCase
{
    use RefreshDatabase, AuthUser;

    #[Test]
    public function can_delete_states(): void
    {
        $this->authenticateUser(['state:delete']);
        $state = State::factory()->create();
        $this->deleteJson(route('api.states.destroy', $state))
            ->assertNoContent();
    }

    #[Test]
    public function guests_cannot_delete_states(): void
    {
        $state = State::factory()->create();
        $this->deleteJson(route('api.states.destroy', $state))
            ->assertJsonApiError(
                title: trans('httpCodes.401'),
                detail: trans('myMessages.ActionRequiresAuth'),
                status: '401'
            );
    }
}

<?php

namespace Tests\Feature\States;

use App\Models\State;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\AuthUser;

class UpdateStateTest extends TestCase
{
    use RefreshDatabase, AuthUser;

    #[Test]
    public function can_update_states(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['state:update']);

        $state = State::factory()->create();

        $response = $this->patchJson(route('api.states.update', $state), [
            'name' => 'test update name state',
            'slug' => $state->slug,
            'color' => '#ffffff',
            'description' => 'test update description state'
        ]);

        $response->assertOk();

        $response->assertJsonApiResource($state, [
            'name' => 'test update name state',
            'slug' => $state->slug,
            'color' => '#ffffff',
            'description' => 'test update description state'
        ]);
    }

    #[Test]
    public function name_is_required()
    {
        $this->authenticateUser(['state:update']);
        $state = State::factory()->create();
        $this->patchJson(route('api.states.update', $state),  [
            'description' => 'test description state'
        ])->assertJsonApiValidationErrors('name');
    }

    #[Test]
    public function slug_is_required()
    {
        $this->authenticateUser(['state:update']);
        $state = State::factory()->create();
        $this->patchJson(route('api.states.update', $state),  [
            'name' => 'test name state',
            'description' => 'test description state'
        ])->assertJsonApiValidationErrors('slug');
    }

    #[Test]
    public function slug_must_be_format_valid()
    {
        $this->authenticateUser(['state:update']);
        $state = State::factory()->create();
        $this->patchJson(route('api.states.update', $state),  [
            'name' => 'test name state',
            'slug' => $state->slug . '-',
            'description' => 'test description state'
        ])->assertJsonApiValidationErrors('slug');
    }

    #[Test]
    public function name_must_be_unique()
    {
        $this->authenticateUser(['state:update']);
        $state = State::factory()->create();
        $state1 = State::factory()->create();

        $this->patchJson(route('api.states.update', $state),  [
            'name' => $state1->name,
            'slug' => $state->slug,
            'description' => 'test description state',
        ])->assertJsonApiValidationErrors('name');
    }

    #[Test]
    public function slug_must_be_unique()
    {
        $this->authenticateUser(['state:update']);
        $state = State::factory()->create();
        $state1 = State::factory()->create();

        $this->patchJson(route('api.states.update', $state),  [
            'name' => 'test name state',
            'slug' => $state1->slug,
            'description' => 'test description state'
        ])->assertJsonApiValidationErrors('slug');
    }

    #[Test]
    public function description_is_required()
    {
        $this->authenticateUser(['state:update']);
        $state = State::factory()->create();
        $this->patchJson(route('api.states.update', $state),  [
            'name' => 'test to update a state'
        ])->assertJsonApiValidationErrors('description');
    }

    #[Test]
    public function color_is_required()
    {
        $this->authenticateUser(['state:update']);
        $state = State::factory()->create();
        $this->patchJson(route('api.states.update', $state),  [
            'name' => 'test name state',
            'description' => 'test description state'
        ])->assertJsonApiValidationErrors('color');
    }

    #[Test]
    public function color_must_be_format_valid()
    {
        $this->authenticateUser(['state:update']);
        $state = State::factory()->create();
        $this->patchJson(route('api.states.update', $state),  [
            'name' => 'test name state',
            'color' => '%&$^$',
            'description' => 'test description state'
        ])->assertJsonApiValidationErrors('color');
    }

    #[Test]
    public function color_must_be_unique()
    {
        $this->authenticateUser(['state:update']);
        $state = State::factory()->create();
        $state1 = State::factory()->create();

        $this->patchJson(route('api.states.update', $state),  [
            'name' => 'test name state',
            'color' => $state1->color,
            'description' => 'test description state',
        ])->assertJsonApiValidationErrors('color');
    }
}

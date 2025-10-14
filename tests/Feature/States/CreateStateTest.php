<?php

namespace Tests\Feature\States;

use App\Models\State;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\AuthUser;

class CreateStateTest extends TestCase
{
    use RefreshDatabase, AuthUser;

    #[Test]
    public function can_create_states(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['state:create']);

        $response = $this->postJson(route('api.states.store'), [
            'name' => 'test to create a state',
            'slug' => 'test-slug',
            'description' => 'test description state',
            'color' =>  '#ffffff'
        ]);
        $response->assertStatus(201);

        $state = State::first();

        $response->assertJsonApiResource($state, [
            'name' => 'test to create a state',
            'slug' => 'test-slug',
            'description' => 'test description state',
            'color' =>  '#ffffff'
        ]);
    }

    #[Test]
    public function name_is_required()
    {
        $this->authenticateUser(['state:create']);
        $this->postJson(route('api.states.store'),  [
            'description' => 'test description state'
        ])->assertJsonApiValidationErrors('name');
    }

    #[Test]
    public function slug_is_required()
    {
        $this->authenticateUser(['state:create']);
        $this->postJson(route('api.states.store'),  [
            'name' => 'test name state',
            'description' => 'test description state'
        ])->assertJsonApiValidationErrors('slug');
    }

    #[Test]
    public function slug_must_be_format_valid()
    {
        $this->authenticateUser(['state:create']);
        $this->postJson(route('api.states.store'),  [
            'name' => 'test name state',
            'slug' => '%&$^$',
            'description' => 'test description state'
        ])->assertJsonApiValidationErrors('slug');
    }

    #[Test]
    public function name_must_be_unique()
    {
        $this->authenticateUser(['state:create']);
        $state = State::factory()->create();

        $this->postJson(route('api.states.store'),  [
            'name' => $state->name,
            'slug' => $state->slug,
            'description' => 'test description state',
        ])->assertJsonApiValidationErrors('name');
    }

    #[Test]
    public function slug_must_be_unique()
    {
        $this->authenticateUser(['state:create']);
        $state = State::factory()->create();

        $this->postJson(route('api.states.store'),  [
            'name' => 'test name state',
            'slug' => $state->slug,
            'description' => 'test description state',
        ])->assertJsonApiValidationErrors('slug');
    }

    #[Test]
    public function description_is_required()
    {
        $this->authenticateUser(['state:create']);
        $this->postJson(route('api.states.store'),  [
            'name' => 'test to create a state'
        ])->assertJsonApiValidationErrors('description');
    }

    #[Test]
    public function color_is_required()
    {
        $this->authenticateUser(['state:create']);
        $this->postJson(route('api.states.store'),  [
            'name' => 'test name state',
            'description' => 'test description state'
        ])->assertJsonApiValidationErrors('color');
    }

    #[Test]
    public function color_must_be_format_valid()
    {
        $this->authenticateUser(['state:create']);
        $this->postJson(route('api.states.store'),  [
            'name' => 'test name state',
            'color' => '%&$^$',
            'description' => 'test description state'
        ])->assertJsonApiValidationErrors('color');
    }

    #[Test]
    public function color_must_be_unique()
    {
        $this->authenticateUser(['state:create']);
        $state = State::factory()->create();

        $this->postJson(route('api.states.store'),  [
            'name' => 'test name state',
            'color' => $state->color,
            'description' => 'test description state',
        ])->assertJsonApiValidationErrors('color');
    }
}

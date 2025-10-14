<?php

namespace Tests\Feature\States;

use App\Models\State;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\AuthUser;

class SparseFieldsStatesTest extends TestCase
{
    use RefreshDatabase, AuthUser;

    #[Test]
    public function specific_fields_can_be_requested_in_the_states_index(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['state:index']);

        $state = State::factory()->create();

        $url = route('api.states.index', [
            'fields' => [
                'states' => 'name,slug'
            ]
        ]);

        $this->getJson($url)
            ->assertJsonFragment([
                'name' => $state->name,
                'slug' => $state->slug
            ])->assertJsonMissing([
                'description' => $state->description,
            ])->assertJsonMissing([
                'description' => null,
            ]);
    }

    #[Test]
    public function route_key_must_be_added_automatically_in_the_states_index(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['state:index']);

        $state = State::factory()->create();

        $url = route('api.states.index', [
            'fields' => [
                'states' => 'name'
            ]
        ]);

        $this->getJson($url)
            ->assertJsonFragment([
                'name' => $state->name
            ])->assertJsonMissing([
                'slug' => $state->slug,
                'description' => $state->description
            ]);
    }

    #[Test]
    public function specific_fields_can_be_requested_in_the_states_show(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['state:show']);

        $state = State::factory()->create();

        $url = route('api.states.show', [
            'state' => $state,
            'fields' => [
                'states' => 'name,slug'
            ]
        ]);

        $this->getJson($url)
            ->assertJsonFragment([
                'name' => $state->name,
                'slug' => $state->slug
            ])->assertJsonMissing([
                'description' => $state->description
            ])->assertJsonMissing([
                'description' => null
            ]);
    }

    #[Test]
    public function route_key_must_be_added_automatically_in_the_states_show(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['state:show']);

        $state = State::factory()->create();

        $url = route('api.states.show', [
            'state' => $state,
            'fields' => [
                'states' => 'name'
            ]
        ]);

        $this->getJson($url)
            ->assertJsonFragment([
                'name' => $state->name
            ])->assertJsonMissing([
                'slug' => $state->slug,
                'description' => $state->description
            ]);
    }
}

<?php

namespace Tests\Feature\States;

use App\Models\State;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\AuthUser;

class FetchStatesTest extends TestCase
{
    use RefreshDatabase, AuthUser;

    #[Test]
    public function can_fetch_a_single_state(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['state:show']);

        $state = State::factory()->create();

        $response = $this->getJson(route('api.states.show', $state));

        $response->assertJsonApiResource($state, [
            'name' => $state->name,
            'slug' => $state->slug,
            'color' => $state->color,
            'description' => $state->description
        ]);
    }

    #[Test]
    public function can_fetch_all_states(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['state:index']);

        $states = State::factory()->count(3)->create();

        $response = $this->getJson(route('api.states.index'));

        $response->assertJsonApiResourceCollection($states, [
            'name',
            'slug',
            'color',
            'description'
        ]);
    }
}

<?php

namespace Tests\Feature\States;

use App\Models\State;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\AuthUser;

class FilterStatesTest extends TestCase
{
    use RefreshDatabase, AuthUser;

    #[Test]
    public function can_filter_states_by_name(): void
    {
        $this->authenticateUser(['state:index']);
        State::factory()->create(['name' => 'C name']);
        State::factory()->create(['name' => 'B name test']);
        State::factory()->create(['name' => 'A name']);

        //states?filter[title]=test

        $url = route('api.states.index', [
            'filter' => [
                'name' => 'test'
            ]
        ]);

        $this->getJson($url)
            ->assertJsonCount(1, 'data')
            ->assertSee('B name test')
            ->assertDontSee([
                'C name',
                'A name'
            ]);
    }

    #[Test]
    public function can_filter_states_by_description(): void
    {
        $this->authenticateUser(['state:index']);
        State::factory()->create(['description' => '0 description 1']);
        State::factory()->create(['description' => '8 description 2']);
        State::factory()->create(['description' => '0 description 3']);

        $url = route('api.states.index', [
            'filter' => [
                'description' => '0'
            ]
        ]);

        $this->getJson($url)
            ->assertJsonCount(2, 'data')
            ->assertSee([
                '0 description 1',
                '0 description 3'
            ]);
    }

    #[Test]
    public function cannot_filter_states_by_unknown(): void
    {
        $this->authenticateUser(['state:index']);
        State::factory(5)->create();

        $url = route('api.states.index', [
            'filter' => [
                'unknown' => '-'
            ]
        ]);

        $this->getJson($url)->assertStatus(400);
    }
}

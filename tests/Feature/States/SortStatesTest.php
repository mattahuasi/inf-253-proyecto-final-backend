<?php

namespace Tests\Feature\States;

use App\Models\State;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\AuthUser;

class SortStatesTest extends TestCase
{
    use RefreshDatabase, AuthUser;

    #[Test]
    public function can_sort_states_by_name(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['state:index']);

        State::factory()->create(['name' => 'C name']);
        State::factory()->create(['name' => 'B name']);
        State::factory()->create(['name' => 'A name']);

        $url = route('api.states.index', ['sort' => 'name']);

        $this->getJson($url)->assertSeeInOrder([
            'A name',
            'B name',
            'C name',
        ]);
    }

    #[Test]
    public function can_sort_states_by_name_desc(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['state:index']);

        State::factory()->create(['name' => 'B name']);
        State::factory()->create(['name' => 'C name']);
        State::factory()->create(['name' => 'A name']);

        $url = route('api.states.index', ['sort' => '-name']);

        $this->getJson($url)->assertSeeInOrder([
            'C name',
            'B name',
            'A name',
        ]);
    }

    #[Test]
    public function can_sort_states_by_description(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['state:index']);

        State::factory()->create(['description' => 'C description']);
        State::factory()->create(['description' => 'B description']);
        State::factory()->create(['description' => 'A description']);

        $url = route('api.states.index', ['sort' => 'description']);

        $this->getJson($url)->assertSeeInOrder([
            'A description',
            'B description',
            'C description',
        ]);
    }

    #[Test]
    public function can_sort_states_by_description_desc(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['state:index']);

        State::factory()->create(['description' => 'B description']);
        State::factory()->create(['description' => 'C description']);
        State::factory()->create(['description' => 'A description']);

        $url = route('api.states.index', ['sort' => '-description']);

        $this->getJson($url)->assertSeeInOrder([
            'C description',
            'B description',
            'A description',
        ]);
    }

    #[Test]
    public function can_sort_states_by_asc_name_and_desc_description(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['state:index']);

        State::factory()->create([
            'name' => 'C name',
            'description' => 'D description',
        ]);
        State::factory()->create([
            'name' => 'A name state',
            'description' => 'A description',
        ]);
        State::factory()->create([
            'name' => 'A name',
            'description' => 'C description',
        ]);

        $url = route('api.states.index', ['sort' => 'name,-description']);

        $this->getJson($url)
            ->assertSeeInOrder([
                'C description',
                'A description',
                'D description',
            ]);
    }

    #[Test]
    public function can_sort_states_by_unknown_fields(): void
    {
        $this->authenticateUser(['state:index']);

        State::factory()->create();
        State::factory()->create();
        State::factory()->create();

        $url = route('api.states.index', ['sort' => 'unknown']);

        $this->getJson($url)
            ->assertStatus(400);
    }
}

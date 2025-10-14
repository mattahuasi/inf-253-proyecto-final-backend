<?php

namespace Tests\Feature\Tables;

use App\Models\Table;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\AuthUser;

class SortTablesTest extends TestCase
{
    use RefreshDatabase, AuthUser;

    private function createTables(): void
    {
        Table::factory()->create(['number' => 4, 'status' => 'A', 'ability' => 6]);
        Table::factory()->create(['number' => 1, 'status' => 'B', 'ability' => 6]);
        Table::factory()->create(['number' => 3, 'status' => 'W', 'ability' => 8]);
        Table::factory()->create(['number' => 2, 'status' => 'A', 'ability' => 4]);
    }

    #[Test]
    public function can_sort_tables_by_number(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['table:index']);
        $this->createTables();

        $url = route('api.tables.index', ['sort' => 'number']);
        $this->getJson($url)->assertSeeInOrder(['B', 'A', 'W', 'A']);
    }

    #[Test]
    public function can_sort_tables_by_number_desc(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['table:index']);
        $this->createTables();

        $url = route('api.tables.index', ['sort' => '-number']);
        $this->getJson($url)->assertSeeInOrder(['A', 'W', 'A', 'B']);
    }

    #[Test]
    public function can_sort_tables_by_status(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['table:index']);
        $this->createTables();

        $url = route('api.tables.index', ['sort' => 'status']);
        $this->getJson($url)->assertSeeInOrder(['A', 'A', 'B', 'W']);
    }

    #[Test]
    public function can_sort_tables_by_status_desc(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['table:index']);
        $this->createTables();

        $url = route('api.tables.index', ['sort' => '-status']);
        $this->getJson($url)->assertSeeInOrder(['W', 'B', 'A', 'A']);
    }

    #[Test]
    public function can_sort_tables_by_ability(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['table:index']);
        $this->createTables();

        $url = route('api.tables.index', ['sort' => 'ability']);

        $this->getJson($url)->assertSeeInOrder(['A',  'B', 'A', 'W']);
    }

    #[Test]
    public function can_sort_tables_by_ability_desc(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['table:index']);
        $this->createTables();

        $url = route('api.tables.index', ['sort' => '-ability']);
        $this->getJson($url)->assertSeeInOrder(['W', 'B', 'A', 'A']);
    }


    #[Test]
    public function can_sort_tables_by_asc_number_and_desc_status(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['table:index']);
        $this->createTables();

        $url = route('api.tables.index', ['sort' => 'number,-status']);

        $this->getJson($url)
            ->assertSeeInOrder(['B', 'A', 'W', 'A']);
    }

    #[Test]
    public function can_sort_tables_by_unknown_fields(): void
    {
        $this->authenticateUser(['table:index']);
        $url = route('api.tables.index', ['sort' => 'unknown']);

        $this->getJson($url)
            ->assertStatus(400);
    }
}

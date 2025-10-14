<?php

namespace Tests\Feature\Tables;

use App\Models\Table;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\AuthUser;

class FilterTablesTest extends TestCase
{
    use RefreshDatabase, AuthUser;

    #[Test]
    public function can_filter_tables_by_number(): void
    {
        $this->authenticateUser(['table:index']);

        Table::factory()->create(['number' => 3]);
        Table::factory()->create(['number' => 1]);
        Table::factory()->create(['number' => 5]);

        $url = route('api.tables.index', [
            'filter' => [
                'number' => 1
            ]
        ]);

        $this->getJson($url)
            ->assertJsonCount(1, 'data')
            ->assertSee(1);

    }

    #[Test]
    public function can_filter_tables_by_status(): void
    {
        $this->authenticateUser(['table:index']);

        Table::factory()->create(['number'=> 1, 'status' => 'B']);
        Table::factory()->create(['number'=> 2, 'status' => 'A']);
        Table::factory()->create(['number'=> 3, 'status' => 'W']);
        Table::factory()->create(['number'=> 4, 'status' => 'A']);

        $url = route('api.tables.index', [
            'filter' => [
                'status' => 'A'
            ]
        ]);

        $this->getJson($url)
            ->assertJsonCount(2, 'data')
            ->assertSee([
                'A',
                'A'
            ]);
    }

    #[Test]
    public function can_filter_tables_by_ability(): void
    {
        $this->authenticateUser(['table:index']);

        Table::factory()->create(['number'=> 1, 'status' => 'B', 'ability' => 6]);
        Table::factory()->create(['number'=> 2, 'status' => 'A', 'ability' => 4]);
        Table::factory()->create(['number'=> 3, 'status' => 'W', 'ability' => 8]);
        Table::factory()->create(['number'=> 4, 'status' => 'A', 'ability' => 6]);

        $url = route('api.tables.index', [
            'filter' => [
                'ability' => 8
            ]
        ]);

        $this->getJson($url)
            ->assertJsonCount(1, 'data')
            ->assertSee([
                'W'
            ]);
    }

    #[Test]
    public function cannot_filter_tables_by_unknown(): void
    {
        $this->authenticateUser(['table:index']);

        $url = route('api.tables.index', [
            'filter' => [
                'unknown' => '-'
            ]
        ]);

        $this->getJson($url)->assertStatus(400);
    }
}

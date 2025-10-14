<?php

namespace Tests\Feature\Tables;

use App\Models\Table;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\AuthUser;

class FetchTablesTest extends TestCase
{
    use RefreshDatabase, AuthUser;

    #[Test]
    public function can_fetch_a_single_table(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['table:show']);

        $table = Table::factory()->create();

        $response = $this->getJson(route('api.tables.show', $table));

        $response->assertJsonApiResource($table, [
            'number' => $table->number,
            'status' => $table->status,
            'ability' => $table->ability,
        ]);
    }

    #[Test]
    public function can_fetch_all_tables(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['table:index']);

        $tables[] = Table::factory()->create(['number' => 10]);
        $tables[] = Table::factory()->create(['number' => 11]);
        $tables[] = Table::factory()->create(['number' => 12]);

        $response = $this->getJson(route('api.tables.index'));

        $response->assertJsonApiResourceCollection($tables, [
            'number',
            'status',
            'ability',
        ]);
    }
}

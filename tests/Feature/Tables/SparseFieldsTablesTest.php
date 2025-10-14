<?php

namespace Tests\Feature\Tables;

use App\Models\Table;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\AuthUser;

class SparseFieldsTablesTest extends TestCase
{
    use RefreshDatabase, AuthUser;

    #[Test]
    public function specific_fields_can_be_requested_in_the_tables_index(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['table:index']);

        $table = Table::factory()->create();

        $url = route('api.tables.index', [
            'fields' => [
                'tables' => 'number,ability'
            ]
        ]);

        $this->getJson($url)
            ->assertJsonFragment([
                'number' => $table->number,
                'ability' => $table->ability
            ])->assertJsonMissing([
                'status' => $table->status
            ]);
    }

    #[Test]
    public function route_key_must_be_added_automatically_in_the_tables_index(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['table:index']);

        $table = Table::factory()->create(
            [
                'number' => 50,
                'ability' => 4,
                'status' => 'A',
            ]
        );

        $url = route('api.tables.index', [
            'fields' => [
                'tables' => 'ability'
            ]
        ]);

        $this->getJson($url)
            ->assertJsonFragment([
                'ability' => $table->ability
            ])->assertJsonMissing([
                'number' => $table->number,
                'status' => $table->status
            ]);
    }

    #[Test]
    public function specific_fields_can_be_requested_in_the_tables_show(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['table:show']);

        $table = Table::factory()->create();

        $url = route('api.tables.show', [
            'table' => $table,
            'fields' => [
                'tables' => 'number,ability'
            ]
        ]);

        $this->getJson($url)
            ->assertJsonFragment([
                'number' => $table->number,
                'ability' => $table->ability
            ])->assertJsonMissing([
                'status' => $table->status
            ]);
    }

    #[Test]
    public function route_key_must_be_added_automatically_in_the_tables_show(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['table:show']);

        $table = Table::factory()->create(
            [
                'number' => 50,
                'ability' => 4,
                'status' => 'A',
            ]
        );

        $url = route('api.tables.show', [
            'table' => $table,
            'fields' => [
                'tables' => 'ability'
            ]
        ]);

        $this->getJson($url)
            ->assertJsonFragment([
                'ability' => $table->ability
            ])->assertJsonMissing([
                'number' => $table->number,
                'status' => $table->status
            ]);
    }
}

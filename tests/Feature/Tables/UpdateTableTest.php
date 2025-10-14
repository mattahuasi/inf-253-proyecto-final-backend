<?php

namespace Tests\Feature\Tables;

use App\Models\Table;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\AuthUser;

class UpdateTableTest extends TestCase
{
    use RefreshDatabase, AuthUser;

    private function updateTable($table, array $data)
    {
        return $this->putJson(route('api.tables.update', $table), $data);
    }

    #[Test]
    public function can_update_tables(): void
    {
        $this->withoutExceptionHandling();

        $this->authenticateUser(['table:update']);

        $table = Table::factory()->create();


        $data = [
            'number' => $table->number,
            'status' => 'B',
            'ability' => 8,
        ];

        $response = $this->updateTable($table, $data);
        $response->assertOk();
        $response->assertJsonApiResource($table, $data);
    }

    #[Test]
    public function number_is_required(): void
    {
        $this->authenticateUser(['table:update']);

        $table = Table::factory()->create();

        $response = $this->updateTable($table, [
            'status' => 'B',
            'ability' => 8,
        ]);

        $response->assertJsonApiValidationErrors('number');
    }

    #[Test]
    public function number_must_be_unique(): void
    {
        $this->authenticateUser(['table:update']);

        $table0 = Table::factory()->create(['number' => 5]);
        $table1 = Table::factory()->create(['number' => 8]);

        $response = $this->updateTable($table0, [
            'number' => $table1->number,
            'status' => 'B',
            'ability' => 8,
        ]);

        $response->assertJsonApiValidationErrors('number');
    }

    #[Test]
    public function status_is_required(): void
    {
        $this->authenticateUser(['table:update']);

        $table = Table::factory()->create();

        $response = $this->updateTable($table, [
            'number' => 1,
            'ability' => 6,
        ]);

        $response->assertJsonApiValidationErrors('status');
    }

    #[Test]
    public function status_invalid_value(): void
    {
        $this->authenticateUser(['table:update']);

        $table = Table::factory()->create();

        $response = $this->updateTable($table, [
            'number' => $table->number,
            'status' => 'invalid',
            'ability' => 8,
        ]);

        $response->assertJsonApiValidationErrors('status');
    }

    #[Test]
    public function ability_is_required(): void
    {
        $this->authenticateUser(['table:update']);

        $table = Table::factory()->create();

        $response = $this->updateTable($table, [
            'number' => 1,
            'status' => 'A',
        ]);

        $response->assertJsonApiValidationErrors('ability');
    }

    #[Test]
    public function ability_invalid_value(): void
    {
        $this->authenticateUser(['table:update']);

        $table = Table::factory()->create();

        $response = $this->updateTable($table, [
            'number' => 1,
            'status' => 'A',
            'ability' => -6,
        ]);

        $response->assertJsonApiValidationErrors('ability');
    }
}

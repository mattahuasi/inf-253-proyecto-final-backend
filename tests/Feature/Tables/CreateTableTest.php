<?php

namespace Tests\Feature\Tables;

use App\Models\Table;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\AuthUser;

class CreateTableTest extends TestCase
{
    use RefreshDatabase, AuthUser;

    private function createTable(array $data)
    {
        return $this->postJson(route('api.tables.store'), $data);
    }

    #[Test]
    public function can_create_tables(): void
    {
        $this->withoutExceptionHandling();

        $this->authenticateUser(['table:create']);

        $data = [
            'number' => 1,
            'status' => 'A',
            'ability' => 6,
        ];

        $response = $this->createTable($data);
        $response->assertStatus(201);

        $table = Table::first();
        $response->assertJsonApiResource($table, $data);
    }

    #[Test]
    public function number_is_required(): void
    {
        $this->authenticateUser(['table:create']);

        $response = $this->createTable([
            'status' => 'A',
            'ability' => 6,
        ]);

        $response->assertJsonApiValidationErrors('number');
    }

    #[Test]
    public function number_must_be_unique(): void
    {
        $this->authenticateUser(['table:create']);

        $table = Table::factory()->create();

        $response = $this->createTable([
            'number' => $table->number,
            'status' => 'A',
            'ability' => 6,
        ]);

        $response->assertJsonApiValidationErrors('number');
    }

    #[Test]
    public function status_is_required(): void
    {
        $this->authenticateUser(['table:create']);

        $response = $this->createTable([
            'number' => 1,
            'ability' => 6,
        ]);

        $response->assertJsonApiValidationErrors('status');
    }

    #[Test]
    public function status_invalid_value(): void
    {
        $this->authenticateUser(['table:create']);

        $response = $this->createTable([
            'number' => 1,
            'status' => 'invalid',
            'ability' => 6,
        ]);

        $response->assertJsonApiValidationErrors('status');
    }

    #[Test]
    public function ability_is_required(): void
    {
        $this->authenticateUser(['table:create']);

        $response = $this->createTable([
            'number' => 1,
            'status' => 'A',
        ]);

        $response->assertJsonApiValidationErrors('ability');
    }

    #[Test]
    public function ability_invalid_value(): void
    {
        $this->authenticateUser(['table:create']);

        $response = $this->createTable([
            'number' => 1,
            'status' => 'A',
            'ability' => -6,
        ]);

        $response->assertJsonApiValidationErrors('ability');
    }
}

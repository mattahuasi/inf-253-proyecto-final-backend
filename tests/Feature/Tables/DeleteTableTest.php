<?php

namespace Tests\Feature\Tables;

use App\Models\Table;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\AuthUser;

class DeleteTableTest extends TestCase
{
    use RefreshDatabase, AuthUser;

    #[Test]
    public function can_delete_tables(): void
    {
        $this->authenticateUser(['table:delete']);
        $table = Table::factory()->create();
        $this->deleteJson(route('api.tables.destroy', $table))
            ->assertNoContent();
    }

    #[Test]
    public function guests_cannot_delete_tables(): void
    {
        $table = Table::factory()->create();
        $this->deleteJson(route('api.tables.destroy', $table))
            ->assertJsonApiError(
                title: trans('httpCodes.401'),
                detail: trans('myMessages.ActionRequiresAuth'),
                status: '401'
            );
    }
}

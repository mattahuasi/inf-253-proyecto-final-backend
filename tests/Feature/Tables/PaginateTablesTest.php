<?php

namespace Tests\Feature\Tables;

use App\Models\Table;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\AuthUser;

class PaginateTablesTest extends TestCase
{
    use RefreshDatabase, AuthUser;

    #[Test]
    public function can_paginate_sorted_tables(): void
    {
        $this->authenticateUser(['table:index']);

        $t1 = Table::factory()->create(['number' => 4, 'status' => 'A', 'ability' => 6]);
        $t2 = Table::factory()->create(['number' => 1, 'status' => 'B', 'ability' => 6]);
        $t3 = Table::factory()->create(['number' => 3, 'status' => 'W', 'ability' => 8]);
        $t4 = Table::factory()->create(['number' => 2, 'status' => 'A', 'ability' => 4]);
        $t6 = Table::factory()->create(['number' => 6, 'status' => 'W', 'ability' => 4]);

        $url = route('api.tables.index', [
            'page' => [
                'size' => 2,
                'number' => 2
            ],
            'sort' => 'number'
        ]);

        $response = $this->getJson($url)
            ->assertSee([
                $t3->number,
                $t1->number,
            ]);

        $response->assertJsonStructure([
            'links' => ['first', 'last', 'prev', 'next']
        ]);

        $firstLink = urldecode($response->json('links.first'));
        $lastLink = urldecode($response->json('links.last'));
        $prevLink = urldecode($response->json('links.prev'));
        $nextLink = urldecode($response->json('links.next'));

        $this->assertStringContainsString('page[size]=2', $firstLink);
        $this->assertStringContainsString('page[number]=1', $firstLink);

        $this->assertStringContainsString('page[size]=2', $lastLink);
        $this->assertStringContainsString('page[number]=3', $lastLink);

        $this->assertStringContainsString('page[size]=2', $prevLink);
        $this->assertStringContainsString('page[number]=1', $prevLink);

        $this->assertStringContainsString('page[size]=2', $nextLink);
        $this->assertStringContainsString('page[number]=3', $nextLink);
    }

    #[Test]
    public function can_paginate_filtered_tables(): void
    {
        $this->authenticateUser(['table:index']);

        Table::factory()->create(['number' => 2, 'status' => 'A', 'ability' => 4]);
        Table::factory()->create(['number' => 3, 'status' => 'W', 'ability' => 8]);
        Table::factory()->create(['number' => 6, 'status' => 'W', 'ability' => 4]);

        $url = route('api.tables.index', [
            'filter[ability]' => 4,
            'page' => [
                'size' => 1,
                'number' => 1
            ]
        ]);

        $response = $this->getJson($url);

        $firstLink = urldecode($response->json('links.first'));
        $lastLink = urldecode($response->json('links.last'));
        $prevLink = urldecode($response->json('links.prev'));
        $nextLink = urldecode($response->json('links.next'));

        $this->assertStringContainsString('filter[ability]=4', $firstLink);
        $this->assertStringContainsString('filter[ability]=4', $lastLink);
        $this->assertStringNotContainsString('filter[ability]=4', $prevLink);
        $this->assertStringContainsString('filter[ability]=4', $nextLink);
    }
}

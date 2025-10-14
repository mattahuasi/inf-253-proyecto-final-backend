<?php

namespace Tests\Feature\Roles;

use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\AuthUser;

class PaginateRolesTest extends TestCase
{
    use RefreshDatabase, AuthUser;

    #[Test]
    public function can_paginate_roles(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['role:index']);

        Role::factory(5)->create();
        $roles = Role::get();

        $url = route('api.roles.index', [
            'page' => [
                'size' => 2,
                'number' => 2
            ],
        ]);

        $response = $this->getJson($url)
            ->assertSee([
                $roles[2]->name,
                $roles[3]->name,
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
    public function can_paginate_sorted_roles(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['role:index']);

        Role::factory()->create(['name' => 'role B']);
        Role::factory()->create(['name' => 'role C']);
        Role::factory()->create(['name' => 'role D']);

        $url = route('api.roles.index', [
            'sort' => 'name',
            'page' => [
                'size' => 1,
                'number' => 2
            ]
        ]);

        $response = $this->getJson($url)
            ->assertSee([
                'role B'
            ])->assertDontSee([
                'role C',
                'role D',
            ]);


        $response->assertJsonStructure([
            'links' => ['first', 'last', 'prev', 'next']
        ]);

        $firstLink = urldecode($response->json('links.first'));
        $lastLink = urldecode($response->json('links.last'));
        $prevLink = urldecode($response->json('links.prev'));
        $nextLink = urldecode($response->json('links.next'));

        $this->assertStringContainsString('sort=name', $firstLink);
        $this->assertStringContainsString('sort=name', $lastLink);
        $this->assertStringContainsString('sort=name', $prevLink);
        $this->assertStringContainsString('sort=name', $nextLink);
    }

    #[Test]
    public function can_paginate_filtered_roles(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['role:index']);

        Role::factory()->create(['name' => 'role B test']);
        Role::factory()->create(['name' => 'role C']);
        Role::factory()->create(['name' => 'role D test']);

        $url = route('api.roles.index', [
            'filter[name]' => 'test',
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

        $this->assertStringContainsString('filter[name]=test', $firstLink);
        $this->assertStringContainsString('filter[name]=test', $lastLink);
        $this->assertStringNotContainsString('filter[name]=test', $prevLink);
        $this->assertStringContainsString('filter[name]=test', $nextLink);
    }
}

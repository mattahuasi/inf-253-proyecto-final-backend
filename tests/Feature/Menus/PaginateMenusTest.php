<?php

namespace Tests\Feature\Menus;

use App\Models\Menu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\AuthUser;

class PaginateMenusTest extends TestCase
{
    use RefreshDatabase, AuthUser;

    #[Test]
    public function can_paginate_menus(): void
    {
        $this->withoutExceptionHandling();
        $this->authenticateUser(['menu:index']);

        $menus = Menu::factory(6)->create();

        $url = route('api.menus.index', [
            'page' => [
                'size' => 2,
                'number' => 2
            ]
        ]);

        $response = $this->getJson($url)
            ->assertSee([
                $menus[2]->slug,
                $menus[3]->slug
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
    public function can_paginate_sorted_menus(): void
    {
        $this->authenticateUser(['menu:index']);

        Menu::factory()->create(['name' => 'C name']);
        Menu::factory()->create(['name' => 'B name']);
        Menu::factory()->create(['name' => 'A name']);

        $url = route('api.menus.index', [
            'sort' => 'name',
            'page' => [
                'size' => 1,
                'number' => 2
            ]
        ]);

        $response = $this->getJson($url)
            ->assertSee([
                'B name'
            ])->assertDontSee([
                'A name',
                'C name'
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
    public function can_paginate_filtered_menus(): void
    {
        $this->authenticateUser(['menu:index']);

        Menu::factory()->create(['name' => 'B name test']);
        Menu::factory()->create(['name' => 'C name']);
        Menu::factory()->create(['name' => 'A name test']);

        $url = route('api.menus.index', [
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

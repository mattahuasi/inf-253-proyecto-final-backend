<?php

namespace Tests\Unit;

use App\JsonApi\MyDocument;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class DocumentTest extends TestCase
{
    // use RefreshDatabase;

    #[Test]
    public function can_create_json_api_document(): void
    {
        // $category = Category::factory()->create();
        $category = Mockery::mock('Category', function ($mock) {
            $mock->shouldReceive('getResourceType')->andReturn('categories');
            $mock->shouldReceive('getRouteKey')->andReturn('category-id');
        });

        $document = MyDocument::type('menus')
            ->id('menu-id')
            ->attributes([
                'name' => 'Menu name'
            ])
            ->relationshipData([
                'category' => $category
            ])
            ->toArray();

        $expected = [
            'data' => [
                'type' => 'menus',
                'id' => 'menu-id',
                'attributes' => [
                    'name' => 'Menu name'
                ],
                'relationships' => [
                    'category' => [
                        'data' => [
                            'type' => 'categories',
                            'id' => 'category-id'
                        ]
                    ]
                ]
            ]
        ];

        $this->assertEquals($expected, $document);
    }
}

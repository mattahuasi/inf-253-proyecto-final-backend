<?php

namespace Tests\Feature;

use App\Http\Middleware\ValidateJsonApiDocument;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ValidateJsonApiDocumentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutJsonApiDocumentFormatting();

        Route::any('api/test_route', fn() => 'OK')->middleware(ValidateJsonApiDocument::class);
    }

    #[Test]
    public function only_accepts_valid_json_api_document(): void
    {
        $this->postJson('api/test_route', [
            'data' => [
                'type' => 'value',
                'attributes' => [
                    'attr1' => "value"
                ]
            ]
        ])->assertSuccessful();

        $this->patchJson('api/test_route', [
            'data' => [
                'id' => '1',
                'type' => 'value',
                'attributes' => [
                    'attr1' => "value"
                ]
            ]
        ])->assertSuccessful();
    }

    #[Test]
    public function data_is_required(): void
    {
        $this->postJson('api/test_route')
            ->assertJsonApiValidationErrors('data');
        $this->patch('api/test_route')
            ->assertJsonApiValidationErrors('data');
    }

    #[Test]
    public function data_mus_be_a_array(): void
    {
        $this->postJson('api/test_route', ['data' => 'string'])
            ->assertJsonApiValidationErrors('data');
        $this->patch('api/test_route', ['data' => 'string'])
            ->assertJsonApiValidationErrors('data');
    }

    #[Test]
    public function data_type_is_required(): void
    {
        $this->postJson('api/test_route', ['data' => [
            'attributes' => [0, 1]
        ]])->assertJsonApiValidationErrors('data.type');
        $this->patchJson('api/test_route', ['data' => [
            'attributes' => [0, 1]
        ]])->assertJsonApiValidationErrors('data.type');
    }

    #[Test]
    public function data_type_mus_be_a_string(): void
    {
        $this->postJson('api/test_route', ['data' =>
        [
            'type' => 0,
            'attributes' => [0, 1]
        ]])->assertJsonApiValidationErrors('data.type');

        $this->patch('api/test_route', ['data' =>
        [
            'type' => 0,
            'attributes' => [0, 1]
        ]])->assertJsonApiValidationErrors('data.type');
    }

    #[Test]
    public function data_attributes_is_required(): void
    {
        $this->postJson('api/test_route', ['data' => [
            'type' => 'string',
        ]])->assertJsonApiValidationErrors('data.attributes');
        $this->patchJson('api/test_route', ['data' => [
            'type' => 'string',
        ]])->assertJsonApiValidationErrors('data.attributes');
    }


    #[Test]
    public function data_attributes_mus_be_a_array(): void
    {
        $this->postJson('api/test_route', ['data' => [
            'type' => 'string',
            'attributes' => 1
        ]])->assertJsonApiValidationErrors('data.attributes');
        $this->patchJson('api/test_route', ['data' => [
            'type' => 'string',
            'attributes' => "0"
        ]])->assertJsonApiValidationErrors('data.attributes');
    }

    #[Test]
    public function data_id_is_required(): void
    {
        $this->patchJson('api/test_route', [
            'data' => [
                'type' => 'string',
                'attributes' => [0, 1]
            ]
        ])->assertJsonApiValidationErrors('data.id');
    }

    #[Test]
    public function data_id_must_be_a_string(): void
    {
        $this->patchJson('api/test_route', [
            'data' => [
                'id' => [1, 1],
                'type' => 'string',
                'attributes' => [0, 1]
            ]
        ])->assertJsonApiValidationErrors('data.id');
    }
}

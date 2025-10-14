<?php

namespace Tests\Feature;

use App\Http\Middleware\ValidateJsonApiHeaders;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class ValidateJsonApiHeadersTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Route::any('api/test_route', function () {
            return 'OK';
        })->middleware(ValidateJsonApiHeaders::class);
    }

    #[Test]
    public function accept_header_must_be_present_in_all_requests(): void
    {
        $this->get('api/test_route')->assertStatus(406);

        $this->get('api/test_route', [
            'Accept' => 'application/vnd.api+json'
        ])->assertSuccessful();
    }

    #[Test]
    public function content_type_header_must_be_present_in_all_post_requests(): void
    {
         $this->post(
            'api/test_route',
            [],
            ['Accept' => 'application/vnd.api+json']
        )->assertStatus(415);

        $this->post(
            'api/test_route',
            [],
            [
                'Accept' => 'application/vnd.api+json',
                'Content-Type' => 'application/vnd.api+json'
            ]
        )->assertSuccessful();
    }

    #[Test]
    public function content_type_header_must_be_present_in_all_patch_requests(): void
    {
        $this->patch(
            'api/test_route',
            [],
            ['Accept' => 'application/vnd.api+json']
        )->assertStatus(415);

        $this->patch(
            'api/test_route',
            [],
            [
                'Accept' => 'application/vnd.api+json',
                'Content-Type' => 'application/vnd.api+json'
            ]
        )->assertSuccessful();
    }

    #[Test]
    public function content_type_header_must_be_present_in_responses(): void
    {
        $this->withoutExceptionHandling();

        $this->get('api/test_route', [
            'Accept' => 'application/vnd.api+json'
        ])->assertHeader('Content-Type', 'application/vnd.api+json');

        $this->post('api/test_route', [], [
            'Accept' => 'application/vnd.api+json',
            'Content-Type' => 'application/vnd.api+json'
        ])->assertHeader('Content-Type', 'application/vnd.api+json');

        $this->patch('api/test_route', [], [
            'Accept' => 'application/vnd.api+json',
            'Content-Type' => 'application/vnd.api+json'
        ])->assertHeader('Content-Type', 'application/vnd.api+json');
    }

    #[Test]
    public function content_type_header_must_not_be_present_in_empty_responses(): void
    {
        $this->withoutExceptionHandling();

        Route::any('empty_response', function () {
            return response()->noContent();
        })->middleware(ValidateJsonApiHeaders::class);

        $this->get('empty_response', [
            'Accept' => 'application/vnd.api+json',
        ])->assertHeaderMissing('Content-Type');

        $this->post('empty_response', [], [
            'Accept' => 'application/vnd.api+json',
            'Content-Type' => 'application/vnd.api+json',
        ])->assertHeaderMissing('Content-Type');

        $this->patch('empty_response', [], [
            'Accept' => 'application/vnd.api+json',
            'Content-Type' => 'application/vnd.api+json',
        ])->assertHeaderMissing('Content-Type');

        $this->delete('empty_response', [], [
            'Accept' => 'application/vnd.api+json',
            'Content-Type' => 'application/vnd.api+json',
        ])->assertHeaderMissing('Content-Type');
    }
}

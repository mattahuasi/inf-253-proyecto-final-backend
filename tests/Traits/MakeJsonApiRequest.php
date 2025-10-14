<?php

namespace Tests\Traits;

use App\JsonApi\MyDocument;
use App\Models\User;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;

trait MakeJsonApiRequest
{
    protected bool $formatJsonApiDocument = true;
    protected int $formatJsonApiDocumentLevel = 1;
    protected bool $withApiKey = false;
    // private function authenticateUser(array $permissions = ['table:index'])
    // {
    //     Sanctum::actingAs(User::factory()->create(), $permissions);
    // }
    //
    // public function authenticateUser(array $permissions = [])

    public function incrementLevelJsonApiDocumentFormatting()
    {
        $this->formatJsonApiDocumentLevel++;
    }

    public function withoutJsonApiDocumentFormatting()
    {
        $this->formatJsonApiDocument = false;
    }

    public function withAuthApiKeyHeader()
    {
        $this->withApiKey = true;
    }
    // public function json($method, $uri, array $data = [], array $headers = [], $options = 0)
    // {
    //     $headers['Accept'] = 'application/vnd.api+json';
    //     return $this->myJsonApi($method, $uri, $data, $headers, $options);
    // }formatJsonApiDocument

    public function json($method, $uri, array $data = [], array $headers = [], $options = 0)
    {
        $headers['Accept'] = 'application/vnd.api+json';

        if ($this->withApiKey) {
            $headers['X-API-KEY'] = env('API_KEY');
        }

        if ($this->formatJsonApiDocument) {
            $url_path = Str::of(parse_url($uri)['path'])->after('api/')->value();
            $url_path = array_chunk(explode('/', $url_path), 2);
            $type = $url_path[$this->formatJsonApiDocumentLevel - 1][0];
            $id = $url_path[$this->formatJsonApiDocumentLevel - 1][1] ?? "";
            $new_data = MyDocument::type($type)
                ->id($id)
                ->attributes($data)
                ->relationshipData($data['_relationships'] ?? [])
                ->toArray();
        }

        return parent::json($method, $uri, $new_data ?? $data, $headers, $options);
    }

    public function postJson($uri, array $data = [], array $headers = [], $options = 0)
    {
        $headers['Content-Type'] = 'application/vnd.api+json';
        return $this->json('POST', $uri, $data, $headers, $options);
    }

    public function patchJson($uri, array $data = [], array $headers = [], $options = 0)
    {
        $headers['Content-Type'] = 'application/vnd.api+json';
        return $this->json('PATCH', $uri, $data, $headers, $options);
    }

    public function deleteJson($uri, array $data = [], array $headers = [], $options = 0)
    {
        $headers['Content-Type'] = 'application/vnd.api+json';
        return $this->json('DELETE', $uri, $data, $headers, $options);
    }
    // public function postJsonApi($uri, array $data = [], array $headers = [], $options = 0): \Illuminate\Testing\TestResponse
    // {
    //     $headers['Content-Type'] = 'application/vnd.api+json';
    //     return $this->myJsonApi('POST', $uri, $data, $headers, $options);
    // }

    // public function patchJsonApi($uri, array $data = [], array $headers = [], $options = 0): \Illuminate\Testing\TestResponse
    // {
    //     $headers['Content-Type'] = 'application/vnd.api+json';
    //     return $this->myJsonApi('PATCH', $uri, $data, $headers, $options);
    // }
}

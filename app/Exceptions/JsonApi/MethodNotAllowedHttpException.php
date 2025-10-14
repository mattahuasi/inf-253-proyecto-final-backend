<?php

namespace App\Exceptions\JsonApi;

use Exception;
use Illuminate\Http\JsonResponse;

class MethodNotAllowedHttpException extends Exception
{
    protected $statusCode = 405;

    public function render($request)
    {
        $data = [
            'errors' => [[
                'status' => (string) $this->statusCode,
                'title' => 'Method Not Allowed',
                'detail' => $this->getMessage(),
            ]]
        ];
        $headers = [
            'Content-Type' => 'application/vnd.api+json'
        ];
        return new JsonResponse($data, $this->statusCode, $headers);
    }
}

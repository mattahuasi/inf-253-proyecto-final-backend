<?php

namespace App\Exceptions\JsonApi;

use Exception;
use Illuminate\Http\JsonResponse;

class UnsupportedMediaTypeHttpException extends Exception
{
    protected $statusCode = 415;

    public function render($request)
    {
        $data = [
            'errors' => [[
                'status' => (string) $this->statusCode,
                'title' => 'Unsupported Media Type',
                'detail' => $this->getMessage(),
            ]]
        ];
        $headers = [
            'Content-Type' => 'application/vnd.api+json'
        ];
        return new JsonResponse($data, $this->statusCode, $headers);
    }
}

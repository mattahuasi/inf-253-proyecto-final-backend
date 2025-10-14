<?php

namespace App\Exceptions\JsonApi;

use Exception;
use Illuminate\Http\JsonResponse;

class NotAcceptableHttpException extends Exception
{
    protected $statusCode = 406;

    public function render($request)
    {
        // "detail" => "El servidor no puede generar una respuesta conforme al formato solicitado.",
        $data = [
            'errors' => [[
                'status' => (string) $this->statusCode,
                'title' => 'Not Acceptable',
                'detail' => $this->getMessage(),
                // "source" => [
                // "pointer" => "/header/Accept"
                // ]
            ]]
        ];
        $headers = [
            'Content-Type' => 'application/vnd.api+json'
        ];
        return new JsonResponse($data, $this->statusCode, $headers);
    }
}

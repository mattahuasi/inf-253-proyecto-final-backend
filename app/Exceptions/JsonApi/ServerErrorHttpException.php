<?php

namespace App\Exceptions\JsonApi;

use Exception;
use Illuminate\Http\JsonResponse;

class ServerErrorHttpException extends Exception
{
    protected $statusCode = 500;

    public function __construct($message = "Server Error", $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function render($request)
    {
        $data = [
            'errors' => [[
                'status' => (string) $this->statusCode,
                'title' => 'Bad Request',
                'detail' => $this->getMessage(),
            ]]
        ];
        $headers = [
            'Content-Type' => 'application/vnd.api+json'
        ];
        return new JsonResponse($data, $this->statusCode, $headers);
    }
}

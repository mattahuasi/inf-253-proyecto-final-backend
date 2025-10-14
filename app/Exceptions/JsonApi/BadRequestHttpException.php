<?php

namespace App\Exceptions\JsonApi;

use Exception;
use Illuminate\Http\JsonResponse;

class BadRequestHttpException extends Exception
{
    protected $statusCode = 400;

    public function __construct($message = "Bad Request", $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function render($request)
    {
        $data = [
            'errors' => [[
                'status' => (string) $this->statusCode,
                'title' => trans('httpCodes.400'),
                'detail' => $this->getMessage(),
            ]]
        ];
        $headers = [
            'Content-Type' => 'application/vnd.api+json'
        ];
        return new JsonResponse($data, $this->statusCode, $headers);
    }
}

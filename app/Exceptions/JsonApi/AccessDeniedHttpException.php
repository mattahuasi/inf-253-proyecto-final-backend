<?php

namespace App\Exceptions\JsonApi;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class AccessDeniedHttpException extends Exception
{
    protected $statusCode = Response::HTTP_FORBIDDEN;

    public function render($request)
    {
        $data = [
            'errors' => [[
                'status' => (string) $this->statusCode,
                'title' => trans('httpCodes.403'),
                'detail' => trans('myMessages.ActionUnauthorized'),
            ]]
        ];
        $headers = [
            'Content-Type' => 'application/vnd.api+json'
        ];
        return new JsonResponse($data, $this->statusCode, $headers);
    }
}

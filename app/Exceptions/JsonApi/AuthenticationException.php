<?php

namespace App\Exceptions\JsonApi;

use Exception;
use Illuminate\Http\JsonResponse;

class AuthenticationException extends Exception
{
    protected $statusCode = 401;

    public function render($request)
    {
        $data = [
            'errors' => [[
                'status' => (string) $this->statusCode,
                'title' => trans('httpCodes.401'),
                'detail' => trans('myMessages.ActionRequiresAuth'),
            ]]
        ];
        // 'title' => trans('httpCodes.401'),
        $headers = [
            'Content-Type' => 'application/vnd.api+json'
        ];
        return new JsonResponse($data, $this->statusCode, $headers);
    }
}

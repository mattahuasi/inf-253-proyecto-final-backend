<?php

namespace App\Exceptions\JsonApi;

use Illuminate\Http\JsonResponse;

class ValidationException extends \Illuminate\Validation\ValidationException
{
    protected $statusCode = 422;

    public function __construct(\Illuminate\Validation\ValidationException $e)
    {
        parent::__construct($e->validator, $e->response, $e->errorBag);
    }

    public function render($request)
    {
        $data = [
            'errors' =>  collect($this->errors())
                ->map(function ($m, $k) {
                    return [
                        'status' =>  (string) $this->statusCode,
                        'title' => "Unprocessable Entity",
                        'detail' => $m[0],
                        'source' => [
                            'pointer' => '/' . str_replace('.', '/', $k)
                        ],
                    ];
                })->values()
        ];
        $headers = [
            'Content-Type' => 'application/vnd.api+json'
        ];
        return new JsonResponse($data, $this->statusCode, $headers);
    }
}

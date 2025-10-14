<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class JsonApiValidationErrorResponse extends JsonResponse
{

    public function __construct(ValidationException $e, $status = 422)
    {
        $data = [
            'errors' =>  collect($e->errors())
                ->map(function ($m, $k) use ($e) {
                    return [
                        'status' => 422,
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

        parent::__construct($data, $status, $headers);
    }
}

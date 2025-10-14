<?php

namespace App\Exceptions\JsonApi;

use Exception;
use Illuminate\Http\JsonResponse;

class NotFoundHttpException extends Exception
{
    protected $statusCode = 404;

    public function __construct($message = "Not Found", $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function render($request)
    {
        $id = $request->input('data.id');
        $type = $request->input('data.type');
        $data = [
            'errors' => [[
                'status' => (string) $this->statusCode,
                'title' => trans('httpCodes.404'),
                'detail' => "No records found with the id '{$id}' in the '{$type}' resource.",
            ]]
        ];
        $headers = [
            'Content-Type' => 'application/vnd.api+json'
        ];
        return new JsonResponse($data, $this->statusCode, $headers);
    }
}

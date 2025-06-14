<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class InvalidInputException extends Exception
{
    protected $message;

    public function __construct(string $message = "", int $code = Response::HTTP_FORBIDDEN) {
        $this->message = $message;
        $this->code = $code;
    }

    public function render($request)
    {
        $response = [
            'success' => false,
            'status' => $this->code,
            'message' => $this->message,
            'data' => [
                'error' => [$this->message],
            ],
        ];

        return response()->json($response, $this->code);
    }
}

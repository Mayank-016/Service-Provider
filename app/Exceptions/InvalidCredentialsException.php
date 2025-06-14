<?php
namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;

class InvalidCredentialsException extends Exception
{
    protected $message = 'These credentials do not match our records.';

    public function __construct()
    {
    }

    /**
     * Prepare the response directly from the exception.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function render($request)
    {
        $response = [
            'success' => false,
            'status' => Response::HTTP_UNAUTHORIZED,
            'message' => $this->message,
            'data' => [
                'error' => [$this->message],
            ],
        ];

        return response()->json($response, Response::HTTP_UNAUTHORIZED);
    }
}

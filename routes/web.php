<?php

use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

Route::get('/', function () {
    return response()->json([
        'success' => true,
        'status' => Response::HTTP_OK,
        'message' => "Service Up",
        'data' => null,
    ], Response::HTTP_OK);
});

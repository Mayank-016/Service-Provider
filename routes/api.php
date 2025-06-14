<?php

use App\Http\Controllers\AuthController;
use App\Http\Middleware\ThrottleLoginRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/login',function(){
    return response()->json([
        'message' => 'Please login !',
    ]);
})->name('login');

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
    Route::post('/login', [AuthController::class, 'login'])->middleware(ThrottleLoginRequests::class)->name('auth.login');
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum')->name('auth.logout');
});
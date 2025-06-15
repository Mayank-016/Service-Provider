<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProviderController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\IsAdminMiddleWare;
use App\Http\Middleware\IsProviderMiddleware;
use App\Http\Middleware\RegisterAdminMiddleWare;
use App\Http\Middleware\ThrottleLoginRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

Route::get('/login', function () {
    return response()->json([
        'success' => false,
        'status' => Response::HTTP_FORBIDDEN,
        'message' => 'Please Login!',
        'data' => null,
    ], Response::HTTP_FORBIDDEN);
})->name('login');

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
    Route::post('/register_admin',[AuthController::class,'registerAdmin'])->middleware(RegisterAdminMiddleWare::class);
    Route::post('/login', [AuthController::class, 'login'])->middleware(ThrottleLoginRequests::class)->name('auth.login');
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum')->name('auth.logout');
});
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/all_services', [ServiceController::class, 'getAllServices']);
    Route::get('/all_categories', [CategoryController::class, 'getAllCategories']);
    Route::get('/all_category_service', [CategoryController::class, 'getAllCategoryService']);
    Route::get('/service_providers',[ServiceController::class,'getServiceProviders']);
    Route::get('/provider_availability',[ProviderController::class,'getProviderAvailability']);

    Route::post('/book_service',[UserController::class,'bookService']);
    Route::post('/cancel_booking',[UserController::class,'cancelBooking']);
    Route::get('/future_bookings',[UserController::class,'getFutureBookings']);
    Route::get('/all_bookings',[UserController::class,'getAllBookings']);
    Route::get('/booking_history',[UserController::class,'getBookingHistory']);
    Route::get('/reporting',[UserController::class,'getReporting']);

    //Admin Specific Routes
    Route::middleware(IsAdminMiddleWare::class)->group(function () {
        Route::post('/add_category',[CategoryController::class,'addCategory']);
    });
    //Provider Specific Routes
    Route::middleware(IsProviderMiddleware::class)->group(function () {
        Route::post('/add_service',[ProviderController::class,'addService']);
        Route::post('/manage_service',[ProviderController::class,'manageServices']);
        Route::post('/manage_availability',[ProviderController::class,'manageAvailability']);
    });
});
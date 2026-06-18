<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\EventSectionsController;
use App\Http\Controllers\Api\SectionController;
use App\Http\Controllers\Api\SeatController;
use App\Http\Controllers\Api\VenueController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\TicketController;
use App\Http\Middleware\IsUserAuth;
use App\Http\Middleware\IsAdmin;    
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

/*
|--------------------------------------------------------------------------
| PUBLIC EVENTS 
|--------------------------------------------------------------------------
*/

Route::apiResource('events', EventController::class)
    ->only(['index', 'show']);

Route::get('events/{id}/seat-map', [EventController::class, 'seatMap']);
Route::get('events/{id}/localidades', [EventController::class, 'localities']);

/*
|--------------------------------------------------------------------------
| PRIVATE ROUTES (AUTH REQUIRED)
|--------------------------------------------------------------------------
*/

Route::middleware([IsUserAuth::class])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | AUTH USER
    |--------------------------------------------------------------------------
    */
    Route::prefix('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'getUser']);
    });

    /*
    |--------------------------------------------------------------------------
    | ADMIN ROUTES
    |--------------------------------------------------------------------------
    */
    Route::middleware([IsAdmin::class])->group(function () {

        Route::apiResource('events', EventController::class)
            ->except(['index', 'show']); // ya están públicas

        Route::get('sections/venue/{venueId}', [SectionController::class, 'listByVenue']);
        Route::apiResource('sections', SectionController::class);

        Route::post('event-sections-price', [EventSectionsController::class, 'store']);

        Route::apiResource('venues', VenueController::class);

        Route::get('seats/sections/{sectionId}', [SeatController::class, 'listBySection']);
        Route::post('seats/generate', [SeatController::class, 'bulkGenerate']);
        Route::apiResource('seats', SeatController::class);
    });
});
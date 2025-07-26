<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\RiderController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\RideController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\DriverAdminController;

/**
 * @group User
 * Get the authenticated user profile.
 * Requires a valid Sanctum token in the Authorization header.
 *
 * @authenticated
 * @header Authorization string required Bearer token. Example: Bearer eyJ0eXAiOiJKV1QiLCJhbGci...
 * @response 200 {
 *   "id": 1,
 *   "name": "Michael Mwanza",
 *   "email": "michael@example.com"
 * }
 */
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

/**
 * @group Authentication
 * APIs for user registration, login, and password reset.
 */
Route::prefix('auth')->group(function () {
    require base_path('routes/auth.php');
});

/**
 * @group Rider
 * APIs for managing rider profile.
 *
 * These routes require Sanctum authentication.
 *
 * @authenticated
 */
Route::middleware(['auth:sanctum', 'can:is-rider'])
    ->prefix('rider')
    ->group(function () {
        Route::get('/', [RiderController::class, 'show']);        // GET /rider
        Route::post('/', [RiderController::class, 'upsert']);     // POST /rider
        Route::delete('/', [RiderController::class, 'destroy']);  // DELETE /rider
        Route::post('/payments', [PaymentController::class, 'store']); // POST /rider/payments
    });

/**
 * @group Driver Profile Management
 * APIs for drivers to manage their own profile data.
 *
 * These routes require Sanctum authentication and the 'driver' role.
 *
 * @authenticated
 */
Route::middleware(['auth:sanctum', 'can:is-driver'])
    ->prefix('driver/profile')
    ->group(function () {
        Route::post('/', [DriverAdminController::class, 'store']); // POST /driver/profile
        Route::patch('/{id}', [DriverAdminController::class, 'update']); // PATCH /driver/profile/{id}
    });

/**
 * @group Driver
 * APIs for drivers to manage assigned rides.
 *
 * These routes require Sanctum authentication.
 *
 * @authenticated
 */
Route::middleware(['auth:sanctum', 'can:is-driver'])
    ->prefix('driver')
    ->group(function () {
        // Ride routes
        Route::get('/rides', [DriverController::class, 'myRides']); // GET /driver/rides
        Route::patch('/rides/{ride}/accept', [DriverController::class, 'acceptRide']); // PATCH /driver/rides/{ride}/accept
        Route::patch('/rides/{ride}/cancel', [DriverController::class, 'cancelRide']); // PATCH /driver/rides/{ride}/cancel
        Route::post('/toggle-status', [DriverController::class, 'toggleOnlineStatus']); // POST /driver/toggle-status
        Route::get('/payments/summary', [PaymentController::class, 'driverMonthlySummary']); // GET /driver/payments/summary

        // Vehicle routes
        Route::get('/vehicles', [VehicleController::class, 'index']);              // GET /driver/vehicles - list vehicles
        Route::post('/vehicles', [VehicleController::class, 'store']);             // POST /driver/vehicles - create vehicle
        Route::get('/vehicles/{vehicle}', [VehicleController::class, 'show']);     // GET /driver/vehicles/{vehicle} - view details
        Route::patch('/vehicles/{vehicle}', [VehicleController::class, 'update']); // PATCH /driver/vehicles/{vehicle} - update vehicle
        Route::delete('/vehicles/{vehicle}', [VehicleController::class, 'destroy']); // DELETE /driver/vehicles/{vehicle} - remove vehicle
    });

/**
 * @group Ride
 * APIs for riders to manage rides.
 *
 * These routes require Sanctum authentication.
 *
 * @authenticated
 */
Route::middleware('auth:sanctum')->prefix('rides')->group(function () {
    Route::get('/', [RideController::class, 'index']);                            // GET /rides
    Route::post('/', [RideController::class, 'store']);                           // POST /rides
    Route::patch('/{ride}/cancel', [RideController::class, 'cancel']);           // PATCH /rides/{ride}/cancel
    Route::patch('/{ride}/late-cancel', [RideController::class, 'lateCancel']);  // PATCH /rides/{ride}/late-cancel
});

/**
 * @group Admin Management
 * Admin-only endpoints for managing users, drivers, and vehicles.
 * Requires authentication and appropriate permissions.
 */
Route::middleware(['auth:sanctum', 'can:is-admin'])
    ->prefix('admin')
    ->group(function () {
        Route::get('/users', [AdminController::class, 'listUsers']); // GET /admin/users
        Route::get('/vehicles', [AdminController::class, 'listVehicles']); // GET /admin/vehicles
        Route::patch('/drivers/{driver}/activate', [AdminController::class, 'activateDriver']); // PATCH /admin/drivers/{driver}/activate
        Route::patch('/drivers/{driver}/deactivate', [AdminController::class, 'deactivateDriver']); // PATCH /admin/drivers/{driver}/deactivate
        Route::patch('/drivers/{driver}/suspend', [AdminController::class, 'suspendDriver']); // PATCH /admin/drivers/{driver}/suspend
        Route::patch('/drivers/{driver}/unsuspend', [AdminController::class, 'unsuspendDriver']); // PATCH /admin/drivers/{driver}/unsuspend
        Route::delete('/users/{user}', [AdminController::class, 'deleteUser']); // DELETE /admin/users/{user}
    });

/**
 * @group Ratings
 * Endpoints for submitting and retrieving ride ratings.
 */
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/ratings', [RatingController::class, 'store']);
    Route::get('/my-ratings', [RatingController::class, 'myRatings']);
    Route::get('/my-average-rating', [RatingController::class, 'myAverageRating']);
});

/**
 * @group Debug
 * Logs incoming request metadata for troubleshooting.
 *
 * Useful for inspecting request headers and host info.
 *
 * @response 200 {
 *   "status": "debug info logged"
 * }
 */
Route::get('/debug', function () {
    Log::info([
        'scheme' => request()->getScheme(),
        'host' => request()->getHost(),
        'headers' => request()->headers->all(),
    ]);
    return response()->json(['status' => 'debug info logged']);
});

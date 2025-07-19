<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**
 * Get the authenticated user.
 *
 * Requires a valid Sanctum token in the Authorization header.
 *
 * @authenticated
 * @response 200 {
 *   "id": 1,
 *   "name": "Michael Mwanza",
 *   "email": "michael@example.com"
 * }
 */
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('auth')->group(function () {
    require base_path('routes/auth.php');
});

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegistrationController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;

// Public routes

/**
 * Register a new user.
 *
 * @bodyParam name string required The user's full name.
 * @bodyParam email string required Must be a valid, unique email.
 * @bodyParam password string required The user’s password.
 * @bodyParam password_confirmation string required Must match password.
 * @response 201 {
 *   "user": { "id": 1, "email": "michael@example.com", "role", "rider" },
 *   "access_token": "token-string",
 *   "token_type": "Bearer"
 * }
 */
Route::post('/register', [RegistrationController::class, 'register']);

/**
 * Log in a user and return an access token.
 *
 * @bodyParam email string required The user’s email.
 * @bodyParam password string required The user’s password.
 * @response 200 {
 *   "user": { "id": 1, "email": "michael@example.com" },
 *   "access_token": "token-string",
 *   "token_type": "Bearer"
 * }
 */
Route::post('/login', [LoginController::class, 'login']);

/**
 * Send password reset link to the user's email.
 *
 * @bodyParam email string required A valid registered email.
 * @response 200 {
 *   "status": "Password reset link sent to your email."
 * }
 */
Route::post('/forgot-password', [PasswordResetLinkController::class, 'sendResetLink']);

/**
 * Reset password using token.
 *
 * @bodyParam email string required The user's email.
 * @bodyParam token string required The reset token.
 * @bodyParam password string required The new password.
 * @bodyParam password_confirmation string required Must match password.
 * @response 200 {
 *   "status": "Your password has been reset!"
 * }
 */
Route::post('/reset-password', [NewPasswordController::class, 'reset']);

/**
 * Handle email verification via secure link.
 *
 * The link is generated and sent via notification.
 *
 * @urlParam id integer required The user’s ID.
 * @urlParam hash string required The SHA1 hash of the email.
 * @response 302 Redirect to frontend with verification status.
 */
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
    ->name('verification.verify')
    ->middleware(['signed']);


// Protected routes

Route::middleware('auth:sanctum')->group(function () {

    /**
     * Log out the authenticated user.
     *
     * @authenticated
     * @response 200 {
     *   "message": "Logged out"
     * }
     */
    Route::post('/logout', [LogoutController::class, 'logout']);

    /**
     * Resend email verification notification.
     *
     * Useful if the initial email was not received.
     *
     * @authenticated
     * @response 202 {
     *   "message": "Verification email sent."
     * }
     */
    Route::post('/email/verify/resend', [EmailVerificationController::class, 'resend']);

    /**
     * Get current authenticated user.
     *
     * Returns user details based on token.
     *
     * @authenticated
     * @response 200 {
     *   "id": 1,
     *   "name": "Michael Mwanza",
     *   "email": "michael@example.com"
     * }
     */
    Route::get('/me', fn() => response()->json(request()->user()));
});

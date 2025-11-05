<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Enums\UserRole;

/**
 * Handles user registration and token issuance.
 */
class RegistrationController extends Controller
{
    /**
     * Register a new user and issue an access token.
     *
     * @group Authentication
     * @bodyParam name string required The user's full name.
     * @bodyParam email string required A valid, unique email address.
     * @bodyParam password string required Must be confirmed.
     * @bodyParam password_confirmation string required Must match password.
     * @bodyParam phone string optional A phone number for contact. Can be null.
     * @bodyParam role string optional The user's role. Must be one of: admin, driver, rider. Defaults to "rider" if not provided.
     *
     * @response 201 {
     *   "user": {
     *     "id": 1,
     *     "name": "Michael Mwanza",
     *     "email": "michael@example.com",
     *     "phone": "0771234567",
     *     "role": "rider"
     *   },
     *   "access_token": "token-string",
     *   "token_type": "Bearer"
     * }
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed'],
            'phone' => ['nullable', 'string'],
            'role' => ['nullable', 'string', Rule::in(array_map(fn($r) => $r->value, UserRole::cases()))],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'], // now stored as nullable
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'] ?? UserRole::Rider->value,
            'email_verified_at' => now(),
        ]);

        // Send email verification turned off because render free tier blocks port 587
        // $user->sendEmailVerificationNotification();

        $token = $user->createToken('registration_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }
}

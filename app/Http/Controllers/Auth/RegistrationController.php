<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;

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
     *
     * @response 201 {
     *   "user": { "id": 1, "email": "michael@example.com" },
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
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->sendEmailVerificationNotification();

        $token = $user->createToken('registration_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }
}

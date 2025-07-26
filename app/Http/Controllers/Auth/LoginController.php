<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Log in the user and return an access token.
     * 
     * @group Authentication
     * 
     * @bodyParam email string required The user’s email.
     * @bodyParam password string required The user’s password.
     * @response 200 {
     *   "user": { "id": 1, "name": "Michael" },
     *   "access_token": "token-string",
     *   "token_type": "Bearer"
     * }
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Optional: Check user role if you use roles
        if ($user->role === 'driver') {
            $driver = Driver::where('user_id', $user->id)->first();

            if (! $driver) {
                return response()->json(['message' => 'Driver profile not found'], 404);
            }

            if (! $driver->is_activated) {
                return response()->json(['message' => 'Driver not activated'], 403);
            }

            if ($driver->is_suspended) {
                return response()->json(['message' => 'Driver is suspended'], 403);
            }

            // Set driver online on login
            $driver->update(['is_online' => true]);
        }

        $token = $user->createToken('login_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }
}

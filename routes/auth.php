<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;

// LOGIN - issue JWT token as HTTP-only cookie
Route::post('/login', function (Request $request) {
    $credentials = $request->only('email', 'password');

    if (!$token = Auth::attempt($credentials)) {
        return response()->json(['error' => 'Invalid credentials'], 401);
    }

    // Set JWT token in secure HttpOnly cookie (7 days)
    return response()->json(['message' => 'Logged in'])
        ->cookie('token', $token, 60 * 24 * 7, '/', null, false, true);
});

// LOGOUT - clear the JWT token cookie
Route::post('/logout', function () {
    return response()->json(['message' => 'Logged out'])
        ->cookie('token', '', -1, '/');
});

// GET CURRENT AUTHENTICATED USER based on JWT token in cookie
Route::get('/user', function (Request $request) {
    $token = $request->cookie('token');

    if (!$token) {
        return response()->json(['error' => 'Not authenticated'], 401);
    }

    try {
        $user = JWTAuth::setToken($token)->authenticate();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        return response()->json($user);
    } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
        return response()->json(['error' => 'Token expired'], 401);
    } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
        return response()->json(['error' => 'Invalid token'], 401);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Could not authenticate user'], 401);
    }
});

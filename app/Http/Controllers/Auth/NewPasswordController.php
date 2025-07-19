<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class NewPasswordController extends Controller
{
    /**
     * Reset user's password using token.
     *
     * @group Password Reset
     * @bodyParam email string required The user's email.
     * @bodyParam token string required The reset token.
     * @bodyParam password string required New password.
     * @bodyParam password_confirmation string required Must match password.
     *
     * @response 200 {
     *   "status": "Your password has been reset!"
     * }
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed'],
        ]);

        $status = Password::reset($request->only('email', 'password', 'password_confirmation', 'token'), function ($user, $password) {
            $user->forceFill([
                'password' => Hash::make($password),
            ])->save();
        });

        return response()->json(['status' => __($status)]);
    }
}

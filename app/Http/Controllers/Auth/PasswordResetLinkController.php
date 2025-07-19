<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Password;

class PasswordResetLinkController extends Controller
{
    /**
     * Send password reset link to user's email.
     *
     * @group Password Reset
     * @bodyParam email string required A registered email.
     *
     * @response 200 {
     *   "status": "Password reset link sent to your email."
     * }
     */
    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);

        $status = Password::sendResetLink($request->only('email'));

        return response()->json(['status' => __($status)]);
    }
}

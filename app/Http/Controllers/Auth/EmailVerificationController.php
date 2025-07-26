<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class EmailVerificationController extends Controller
{
    /**
     * Resend verification email if user's email is not verified.
     *
     * @group Email Verification
     * @authenticated
     * @header Authorization string required Bearer token used to authenticate the request. Example: "Bearer your-token"
     *
     * @response 202 {
     *   "message": "Verification email sent."
     * }
     * @response 200 {
     *   "message": "Email already verified."
     * }
     */
    public function resend(Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified'], 200);
        }

        $user->sendEmailVerificationNotification();

        return response()->json(['message' => 'Verification email sent'], 202);
    }
}

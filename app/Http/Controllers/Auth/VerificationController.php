<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\User;

class VerificationController extends Controller
{
    /**
     * Verify the user's email via link.
     *
     * This endpoint is accessed from the verification email. Redirects to frontend.
     *
     * @group Email Verification
     * @urlParam id integer required The user ID.
     * @urlParam hash string required SHA1 hash of the user's email.
     * @response 302 Redirect to frontend with verification status.
     */
    public function verify(Request $request)
    {
        $user = User::findOrFail($request->route('id'));

        if (! hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
            return response()->json(['message' => 'Invalid verification link'], 403);
        }

        if ($user->hasVerifiedEmail()) {
            return redirect(config('app.frontend_url') . '/email-verified?status=already');
        }

        $user->markEmailAsVerified();
        event(new Verified($user));

        return redirect(config('app.frontend_url') . '/email-verified?status=success');
    }
}

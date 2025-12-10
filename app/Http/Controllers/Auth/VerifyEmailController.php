<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    /**
     * Mark the user's email address as verified.
     * This works without authentication by using the signed URL and hash verification.
     */
    public function __invoke(Request $request, $id, $hash): JsonResponse|RedirectResponse
    {
        $user = User::findOrFail($id);

        // Verify the hash matches the user's email
        if (! hash_equals((string) $hash, sha1($user->email))) {
            return $request->expectsJson()
                ? response()->json(['message' => 'Invalid verification link'], 403)
                : redirect(config('app.frontend_url').'/email-verification-failed');
        }

        // Check if already verified
        if ($user->hasVerifiedEmail()) {
            return $request->expectsJson()
                ? response()->json(['message' => 'Email already verified'], 409)
                : redirect(config('app.frontend_url').'/dashboard?verified=1');
        }

        // Mark email as verified
        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return $request->expectsJson()
            ? response()->json(['message' => 'Email verified successfully'])
            : redirect(config('app.frontend_url').'/dashboard?verified=1');
    }
}

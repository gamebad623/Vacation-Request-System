<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    // public function store(Request $request): JsonResponse|RedirectResponse
    // {
    //     if ($request->user()->hasVerifiedEmail()) {
    //         return redirect()->intended('/dashboard');
    //     }

    //     $request->user()->sendEmailVerificationNotification();

    //     return response()->json(['status' => 'verification-link-sent']);
    // }

    public function verify(EmailVerificationRequest $request){
        if($request->user()->hasVerifiedEmail()){
            return response()->json(['message' => "Already verfied"] , 409);
        }

        $request->user()->markEmailAsVerified();

        return response()->json(['message' => 'Email verfied successfully']);
    }

    public function resend(Request $request){

        if($request->user()->hasVerifiedEmail()){
            return response()->json(['message' => "Already verfied"] , 409);
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json(['message' => 'Verification email resent']);
    }
}

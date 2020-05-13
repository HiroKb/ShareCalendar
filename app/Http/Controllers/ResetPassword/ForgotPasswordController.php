<?php

namespace App\Http\Controllers\ResetPassword;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;

    public function sendResetLinkEmail(Request $request)
    {
        $this->validateEmail($request);

        $this->broker()->sendResetLink(
            $request->only('email')
        );

        return response()->json([], 200);
    }
}

<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;

class TestUserLoginController extends LoginController
{

    public function login(Request $request)
    {
        if ($this->guard()->attempt([
            'email' => config('test_user.email'),
            'password' => config('test_user.password')
        ])){
            return $this->sendLoginResponse($request);
        }

        return $this->sendFailedLoginResponse($request);
    }
}

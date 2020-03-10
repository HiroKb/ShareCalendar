<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserNameRequest;
use App\Http\Requests\UpdateUserPasswordRequest;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * ユーザーネーム変更
     * @param UpdateUserNameRequest $request
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function updateName(UpdateUserNameRequest $request)
    {

        $user = Auth::user();

        $user->name = $request->name;

        $user->save();

        return Auth::user();
    }

    public function updatePassword(UpdateUserPasswordRequest $request)
    {
        $user = Auth::user();

        $user->password = Hash::make($request->new_password);

        $user->save();

        return response()->json();
    }
}

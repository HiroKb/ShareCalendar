<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserEmailRequest;
use App\Http\Requests\UpdateUserNameRequest;
use App\Http\Requests\UpdateUserPasswordRequest;
use App\Models\User;
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

    /**
     * ユーザーのメールアドレス変更
     * @param UpdateUserEmailRequest $request
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function updateEmail(UpdateUserEmailRequest $request)
    {
        $user = Auth::user();

        $user->email = $request->email;

        $user->save();

        return Auth::user();
    }

    /**
     * ユーザーのパスワード変更
     * @param UpdateUserPasswordRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePassword(UpdateUserPasswordRequest $request)
    {
        $user = Auth::user();

        $user->password = Hash::make($request->new_password);

        $user->save();

        return response()->json();
    }

    public function destroy()
    {
        $user = Auth::user();
        $user->delete();
        return response([], 200);
    }
}

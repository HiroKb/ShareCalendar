<?php

namespace App\Http\Controllers\CustomizedAuth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RegisterController extends \App\Http\Controllers\Auth\RegisterController
{
    /**
     * オーバーライド
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
        ]);
    }

    /**
     * オーバーライド
     * 登録後の処理を変更
     * @param Request $request
     * @param $user
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response|mixed
     */
    protected function registered(Request $request, $user)
    {
        return response($user->private_data, 201);
    }
}

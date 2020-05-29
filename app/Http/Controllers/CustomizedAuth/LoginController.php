<?php

namespace App\Http\Controllers\CustomizedAuth;

use Illuminate\Http\Request;


class LoginController extends \App\Http\Controllers\Auth\LoginController
{
    /**
     * オーバーライド
     * ログイン後の処理を変更
     * @param Request $request
     * @param mixed $user
     * @return array|mixed
     */
    public function authenticated(Request $request, $user)
    {
        return $user->private_data;
    }

    /**
     * オーバーライド
     * ログアウト後の処理を変更
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    protected function loggedOut(Request $request)
    {
//        セッションを再生成
        $request->session()->regenerate();
        return response()->json();
    }
}

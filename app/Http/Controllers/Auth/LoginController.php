<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * AuthenticatesUsersトレイトのメソッドをオーバーライド
     * 認証後の挙動を変更(ログインユーザーデータを返却)
     * @param Request $request
     * @param $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        return $user;
    }

    /**
     * AuthenticatesUsersトレイトのメソッドをオーバーライド
     * ログアウト後の挙動を変更
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    protected function loggedOut(Request $request)
    {
//        セッションを再生成
        $request->session()->regenerate();

//        レスポンスを返却
        return response()->json();
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    //
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
        $this->middleware('guest:admin')->except('logout');
    }

    protected function guard()
    {
        return Auth::guard('admin');
    }

    public function username()
    {
        return 'name';
    }

    /**
     * オーバーライド
     * ログイン後の処理を変更
     * @param Request $request
     * @param mixed $user
     * @return array|mixed
     */
    public function authenticated(Request $request, $user)
    {
        return view('admin.index');
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

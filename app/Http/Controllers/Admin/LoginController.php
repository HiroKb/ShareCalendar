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

//    使用するガードの変更
    protected function guard()
    {
        return Auth::guard('admin');
    }

//    ログイン時に使用するカラムの変更
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
}

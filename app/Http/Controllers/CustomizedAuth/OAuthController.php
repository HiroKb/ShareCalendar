<?php

namespace App\Http\Controllers\CustomizedAuth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class OAuthController extends Controller
{
    /**
     * OAuth認証画面ににリダイレクト
     * @param $provider
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirect($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    /**
     * OAuthコールバック・認証結果を受け取りログインor登録
     * @param $provider
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function handleProviderCallback($provider)
    {
        try {
            $user = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect('/');
        }

        Auth::login(User::firstOrCreate([
            'email' => $user->getEmail()
        ],[
            'name' => $user->getName()
        ]));

        return redirect('/');
    }
}

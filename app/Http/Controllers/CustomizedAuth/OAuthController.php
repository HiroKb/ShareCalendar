<?php

namespace App\Http\Controllers\CustomizedAuth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
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
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect('/');
        }

//        Auth::login(User::firstOrCreate([
//            'provider_name' => $provider,
//            'provider_id' => $user->getId()
//        ],[
//            'name' => $user->getName(),
//            'email_verified_at' => Carbon::now()
//        ]));

        $user = User::where([
            'provider_name' => $provider,
            'provider_id' => $socialUser->getId()
        ])->first();

        if ($user !== null) {
            logger($user);
            Auth::login($user);
            return redirect('/');
        }

        $newUser = new User();
        $newUser->name = $socialUser->getName();
        $newUser->provider_name = $provider;
        $newUser->provider_id = $socialUser->getId();
        $newUser->email_verified_at = Carbon::now();
        $newUser->save();

        logger($newUser);
        Auth::login($newUser);
        return redirect('/');
    }
}

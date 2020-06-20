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
            return redirect('/auth');
        }

        $user = User::where([
            'provider_name' => $provider,
            'provider_id' => $socialUser->getId()
        ])->first();

        if ($user !== null) {
            if ($user->provider_email !== $socialUser->getEmail()){
                $user->provider_email = $socialUser->getEmail();
                $user->save();
            }
            Auth::login($user);
            return redirect('/');
        }

        $newUser = new User();
        $newUser->name = !!$socialUser->getName() ? $socialUser->getName() : $socialUser->getNickname();
        $newUser->provider_name = $provider;
        $newUser->provider_id = $socialUser->getId();
        $newUser->provider_email = $socialUser->getEmail();
        $newUser->email_verified_at = Carbon::now();
        $newUser->save();

        Auth::login($newUser);
        return redirect('/');
    }
}

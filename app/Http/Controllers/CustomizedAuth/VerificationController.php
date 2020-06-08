<?php
namespace App\Http\Controllers\CustomizedAuth;

use Illuminate\Http\Request;

class VerificationController extends \App\Http\Controllers\Auth\VerificationController
{
    /**
     * リダイレクト先オーバーライド
     * @var string
     */
    protected $redirectTo = '/';


    /**
     * 確認メール再送処理オーバーライド
     * @param Request $request
     * @return array|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response([], 404);
        }

        $request->user()->sendEmailVerificationNotification();

        return [];
    }
}

<?php
namespace App\Http\Controllers\CustomizedAuth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerificationController extends Controller
{

    use VerifiesEmails;
    /**
     * リダイレクト先オーバーライド
     * @var string
     */
    protected $redirectTo = '/';


    public function __construct()
    {
        $this->middleware('auth.redirect');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

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

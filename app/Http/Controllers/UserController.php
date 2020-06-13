<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImageRequest;
use App\Http\Requests\UpdateEmailRequest;
use App\Http\Requests\UpdateUserNameRequest;
use App\Http\Requests\UpdateUserPasswordRequest;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function getUserData()
    {
        $user = Auth::user();
        return $user !== null ? $user->private_data : [];
    }
    /**
     * ユーザー名変更
     * @param UpdateUserNameRequest $request
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function updateName(UpdateUserNameRequest $request)
    {
        return Auth::user()->updateName($request);
    }

    /**
     * ユーザー画像更新
     * @param ImageRequest $request
     * @param UserService $userService
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function updateImage(ImageRequest $request, UserService $userService)
    {
        return $userService->updateImage($request);
    }

    /**
     * メールアドレス変更確認メール送信
     * @param UpdateEmailRequest $request
     * @param UserService $userService
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function sendUpdateEmailLink(UpdateEmailRequest $request, UserService $userService)
    {
        if (Auth::user()->email === null) {
            return response([], 404);
        }
        return $userService->sendUpdateEmailLink($request);
    }

    /**
     * メールアドレス変更処理
     * @param $token
     * @param UserService $userService
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function updateEmail($token, UserService $userService)
    {
        if (Auth::user()->email === null) {
            return response([], 404);
        }
        return $userService->updateEmail($token);
    }

    /**
     * パスワード変更
     * @param UpdateUserPasswordRequest $request
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function updatePassword(UpdateUserPasswordRequest $request)
    {
        $user = Auth::user();
        if ($user->password === null) {
            return response([], 404);
        }
        return $user->updatePassword($request);
    }

    /**
     * ユーザー削除処理
     * @param UserService $userService
     * @return array|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function destroy(UserService $userService)
    {
        return $userService->deleteAccount();
    }

    /**
     * $fromから$untilまでの個人スケジュールと共有スケジュールの一覧
     * @param $from
     * @param $until
     * @param UserService $userService
     * @return \Illuminate\Support\Collection
     */
    public function schedulesList($from, $until, UserService $userService)
    {
        return $userService->getSchedulesList($from, $until);
    }

    /**
     * 参加している共有カレンダーの一覧
     * @return mixed
     */
    public function sharedCalendarsList(UserService $userService)
    {
        return $userService->getSharedCalendarsList();
    }
}

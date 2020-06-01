<?php

namespace App\Http\Controllers;

use App\Http\Requests\PasswordRequest;
use App\Http\Requests\UpdateUserEmailRequest;
use App\Http\Requests\UpdateUserNameRequest;
use App\Http\Requests\UpdateUserPasswordRequest;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function getUserData()
    {
        $user = Auth::user();
        return $user ? $user->private_data : null;
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
     * ユーザーのメールアドレス変更
     * @param UpdateUserEmailRequest $request
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function updateEmail(UpdateUserEmailRequest $request)
    {
        return Auth::user()->updateEmail($request);
    }

    /**
     * ユーザーのパスワード変更
     * @param UpdateUserPasswordRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePassword(UpdateUserPasswordRequest $request)
    {
        return Auth::user()->updatePassword($request);
    }

    /**
     * パスワード新規登録(OAuthログインユーザー用)
     * @param PasswordRequest $request
     * @return mixed
     */
    public function registrationPassword(PasswordRequest $request)
    {
        return Auth::user()->registrationPassword($request);
    }

    /**
     * ユーザー削除処理
     * @return array
     */
    public function destroy()
    {
        Auth::user()->delete();
        return [];
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

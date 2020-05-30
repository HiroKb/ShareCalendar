<?php

/*
|--------------------------------------------------------------------------
| InternalApi Routes
|--------------------------------------------------------------------------
*/

// ログインユーザー返却
Route::get('/users', 'userController@getUserData')->name('user');
// トークンリフレッシュ
Route::get('/refresh-token', function (\Illuminate\Http\Request $request) {
    $request->session()->regenerateToken();
    return response()->json();
});

/**
 * 未認証の場合のみ許可
 */
Route::group(['middleware' => 'api.guest'], function () {
    // 新規登録
    Route::post('/register', 'CustomizedAuth\RegisterController@register')->name('register');
    // ログイン
    Route::post('/login', 'CustomizedAuth\LoginController@login')->name('login');
    // パスワードリセットメール送信
    Route::post('/password/reset/link', 'CustomizedAuth\ForgotPasswordController@sendResetLinkEmail');
    // パスワードリセット
    Route::post('/password/reset', 'CustomizedAuth\ResetPasswordController@reset');
});

/**
 * 認証済みの場合許可
 */
Route::group(['middleware' => 'api.auth'], function () {
    // ログアウト
    Route::post('/logout', 'CustomizedAuth\LoginController@logout')->name('logout');
    // ユーザーネーム変更
    Route::patch('/users/name', 'UserController@updateName');
    // メールアドレス変更
    Route::patch('/users/email', 'UserController@updateEmail');
    // ユーザーパスワード変更
    Route::patch('/users/password', 'UserController@updatePassword');
    // アカウント削除
    Route::delete('/users', 'UserController@destroy');
    // 個人スケジュールと共有カレンダーのスケジュールリスト取得
    Route::get('/schedules/{from}/{until}', 'UserController@schedulesList');
    // 共有しているカレンダーリストを取得
    Route::get('/shared-calendars/list', 'UserController@sharedCalendarsList');


    // スケジュール登録
    Route::post('/schedules', 'ScheduleController@store');
    // スケジュール更新
    Route::patch('/schedules/{schedule}', 'ScheduleController@update');
    // スケジュール削除
    Route::delete('/schedules/{schedule}', 'ScheduleController@destroy');

    // 共有カレンダーデータ
    Route::get('/shared-calendars/{sharedCalendar}', 'SharedCalendarController@show');
    // 共有カレンダー作成
    Route::post('/shared-calendars', 'SharedCalendarController@store');
    // 共有カレンダー名変更
    Route::patch('/shared-calendars/{sharedCalendar}/name', 'SharedCalendarController@updateName');
    // 共有カレンダー検索ID変更
    Route::patch('/shared-calendars/{sharedCalendar}/search-id', 'SharedCalendarController@updateSearchId');
    // 共有カレンダー削除
    Route::delete('/shared-calendars/{sharedCalendar}', 'SharedCalendarController@destroy');
    // カレンダー共有メンバー
    Route::get('/shared-calendars/{sharedCalendar}/members', 'SharedCalendarController@membersList');
    // カレンダー共有申請者
    Route::get('/shared-calendars/{sharedCalendar}/applications', 'SharedCalendarController@applicationsList');
    // カレンダー共有申請許可
    Route::put('/shared-calendars/{sharedCalendar}/members', 'SharedCalendarController@allowApplication');
    // カレンダー共有申請拒否
    Route::delete('/shared-calendars/{sharedCalendar}/applications', 'SharedCalendarController@rejectApplication');
    // カレンダー共有解除
    Route::delete('/shared-calendars/{sharedCalendar}/members/{memberId?}', 'SharedCalendarController@removeMember');

    // 共有カレンダー検索
    Route::get('/shared-calendars/{searchId}/search', 'SharedCalendarController@search');
    // カレンダー共有申請
    Route::put('/shared-calendars/{searchId}/applications', 'SharedCalendarController@application');

    // 共有スケジュール一覧
    Route::get('/shared-calendars/{sharedCalendar}/schedules/{from}/{until}', 'SharedScheduleController@index');
    // 共有スケジュール作成
    Route::post('/shared-calendars/{sharedCalendar}/schedules', 'SharedScheduleController@store');
    // 共有スケジュール更新
    Route::patch('/shared-calendars/{sharedCalendar}/schedules/{sharedSchedule}', 'SharedScheduleController@update');
    //共有スケジュール削除
    Route::delete('/shared-calendars/{sharedCalendar}/schedules/{sharedSchedule}', 'SharedScheduleController@destroy');

    // チャットメッセージ一覧
    Route::get('/shared-calendars/{sharedCalendar}/chat/messages', 'ChatMessageController@index');
    // チャットメッセージ作成
    Route::post('/shared-calendars/{sharedCalendar}/chat/messages', 'ChatMessageController@store');
});


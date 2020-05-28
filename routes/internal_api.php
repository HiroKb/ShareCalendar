<?php

/*
|--------------------------------------------------------------------------
| InternalApi Routes
|--------------------------------------------------------------------------
*/

// ログインユーザー返却
Route::get('/users', fn() => Auth::user())->name('user');
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
    Route::post('/register', 'Auth\RegisterController@register')->name('register');
// ログイン
    Route::post('/login', 'Auth\LoginController@login')->name('login');
// パスワードリセットメール送信
    Route::post('/password/reset/link', 'ResetPassword\ForgotPasswordController@sendResetLinkEmail');
// パスワードリセット
    Route::post('/password/reset', 'ResetPassword\ResetPasswordController@reset');
});

/**
 * 認証済みの場合許可
 */
Route::group(['middleware' => 'api.auth'], function () {
// ログアウト
    Route::post('/logout', 'Auth\LoginController@logout')->name('logout');
// ユーザーネーム変更
    Route::patch('/users/name', 'UserController@updateName');
// メールアドレス変更
    Route::patch('/users/email', 'UserController@updateEmail');
// ユーザーパスワード変更
    Route::patch('/users/password', 'UserController@updatePassword');
// アカウント削除
    Route::delete('/users', 'UserController@destroy');


//登録スケジュール取得
    Route::get('/schedules/{from}/{until}', 'ScheduleController@list');
// スケジュール登録
    Route::post('/schedules', 'ScheduleController@create');
// スケジュール更新
    Route::patch('/schedules/{schedule}', 'ScheduleController@update');
// スケジュール削除
    Route::delete('/schedules/{schedule}', 'ScheduleController@destroy');

// 共有カレンダー作成
    Route::post('/shared-calendars', 'SharedCalendarController@create');
// 共有カレンダー削除
    Route::delete('/shared-calendars/{sharedCalendar}', 'SharedCalendarController@destroy');
// 参加共有カレンダー一覧
    Route::get('/shared-calendars/list', 'SharedCalendarController@list');
// 共有カレンダーデータ
    Route::get('/shared-calendars/{sharedCalendar}', 'SharedCalendarController@index');
// 共有カレンダー名変更
    Route::patch('/shared-calendars/{sharedCalendar}/name', 'SharedCalendarController@updateName');
// 共有カレンダー検索ID変更
    Route::patch('/shared-calendars/{sharedCalendar}/search-id', 'SharedCalendarController@updateSearchId');
// カレンダー共有メンバー
    Route::get('/shared-calendars/{sharedCalendar}/members', 'SharedCalendarController@membersList');
// カレンダー共有解除
    Route::delete('/shared-calendars/{sharedCalendar}/members/{memberId?}', 'SharedCalendarController@unShare');
// カレンダー共有申請者
    Route::get('/shared-calendars/{sharedCalendar}/applications', 'SharedCalendarController@applicationsList');
// カレンダー共有メンバー追加(共有申請許可
    Route::put('/shared-calendars/{sharedCalendar}/members', 'SharedCalendarController@allowApplication');
// カレンダー共有申請拒否
    Route::delete('/shared-calendars/{sharedCalendar}/applications', 'SharedCalendarController@rejectApplication');

// 共有カレンダー検索
    Route::get('/shared-calendars/{searchId}/search', 'SharedCalendarController@search');
// カレンダー共有申請
    Route::put('/shared-calendars/{searchId}/applications', 'SharedCalendarController@application');

// 共有スケジュール作成
    Route::post('/shared-calendars/{sharedCalendar}/schedules', 'SharedScheduleController@create');
//共有スケジュール削除
    Route::delete('/shared-calendars/{sharedCalendar}/schedules/{sharedSchedule}', 'SharedScheduleController@destroy');
    Route::patch('/shared-calendars/{sharedCalendar}/schedules/{sharedSchedule}', 'SharedScheduleController@update');
// 共有スケジュールリスト取得
    Route::get('/shared-calendars/{sharedCalendar}/schedules/{from}/{until}', 'SharedScheduleController@list');

// チャットメッセージ一覧
    Route::get('/shared-calendars/{sharedCalendar}/chat/messages', 'ChatMessageController@list');
// チャットメッセージ作成
    Route::post('/shared-calendars/{sharedCalendar}/chat/messages', 'ChatMessageController@create');
});


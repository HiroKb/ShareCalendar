<?php

/*
|--------------------------------------------------------------------------
| InternalApi Routes
|--------------------------------------------------------------------------
*/

// 新規登録
Route::post('/register', 'Auth\RegisterController@register')->name('register');
// ログイン
Route::post('/login', 'Auth\LoginController@login')->name('login');
// ログアウト
Route::post('/logout', 'Auth\LoginController@logout')->name('logout');
// ログインユーザー返却
Route::get('/users', fn() => Auth::user())->name('user');
// ユーザーネーム変更
Route::patch('/users/name', 'UserController@updateName');
// メールアドレス変更
Route::patch('/users/email', 'UserController@updateEmail');
// ユーザーパスワード変更
Route::patch('/users/password', 'UserController@updatePassword');


//登録スケジュール取得
Route::get('/schedules/{from}/{until}', 'ScheduleController@index');
// スケジュール登録
Route::post('/schedules', 'ScheduleController@create');
// スケジュール更新
Route::patch('/schedules/{schedule}', 'ScheduleController@update');
// スケジュール削除
Route::delete('/schedules/{schedule}', 'ScheduleController@destroy');

// 共有カレンダー作成
Route::post('/shared-calendars', 'SharedCalendarController@create');
// 参加共有カレンダー一覧
Route::get('/shared-calendars/list', 'SharedCalendarController@list');
// 共有カレンダーデータ
Route::get('/shared-calendars/{sharedCalendar}', 'SharedCalendarController@index');
// カレンダー共有メンバー
Route::get('/shared-calendars/{sharedCalendar}/members', 'SharedCalendarController@membersList');
// カレンダー共有申請者
Route::get('/shared-calendars/{sharedCalendar}/applications', 'SharedCalendarController@applicationsList');
// カレンダー共有申請許可
Route::put('/shared-calendars/{sharedCalendar}/applications/{applicantId}', 'SharedCalendarController@allowApplication');
// カレンダー共有申請拒否
Route::delete('/shared-calendars/{sharedCalendar}/applications/{applicantId}', 'SharedCalendarController@rejectApplication');

// 共有カレンダー検索
Route::get('/shared-calendars/{searchId}/search', 'SharedCalendarController@search');
// カレンダー共有申請
Route::put('/shared-calendars/{searchId}/applications', 'SharedCalendarController@application');

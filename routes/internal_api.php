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
Route::get('/user', fn() => Auth::user())->name('user');
// ユーザーネーム変更
Route::patch('/user-name', 'UserController@updateName');
// ユーザーパスワード変更
Route::patch('/user-password', 'UserController@updatePassword');
// メールアドレス変更
Route::patch('/user-email', 'UserController@updateEmail');


//登録スケジュール取得
Route::get('/schedule/{from}/{until}', 'ScheduleController@index');
// スケジュール登録
Route::post('/schedule', 'ScheduleController@create');
// スケジュール更新
Route::patch('/schedule/{schedule}', 'ScheduleController@update');
// スケジュール削除
Route::delete('/schedule/{schedule}', 'ScheduleController@destroy');

// 共有カレンダー作成
Route::post('/shared-calendar', 'SharedCalendarController@create');
// 参加共有カレンダー一覧
Route::get('/shared-calendar/list', 'SharedCalendarController@list');
// 共有カレンダーデータ
Route::get('/shared-calendar/{SharedCalendar}', 'SharedCalendarController@index');
// カレンダー共有メンバー
Route::get('/shared-calendar/{SharedCalendar}/members', 'SharedCalendarController@membersList');
// カレンダー共有申請者
Route::get('/shared-calendar/{SharedCalendar}/applicants', 'SharedCalendarController@applicantsList');
// カレンダー共有申請許可
Route::put('/shared-calendars/{SharedCalendar}/applications/{applicantId}', 'SharedCalendarController@allowApplication');
// カレンダー共有申請拒否
Route::delete('/shared-calendars/{SharedCalendar}/applications/{applicantId}', 'SharedCalendarController@rejectApplication');

// 共有カレンダー検索
Route::get('/shared-calendars/{searchId}/search', 'SharedCalendarController@search');
// カレンダー共有申請
Route::put('/shared-calendars/{searchId}/applications', 'SharedCalendarController@application');

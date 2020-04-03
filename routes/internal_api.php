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

// 参加共有カレンダー一覧
Route::get('/shared-calendar/list', 'SharedCalendarController@list');

// 共有カレンダー作成
Route::post('/shared-calendar', 'SharedCalendarController@create');

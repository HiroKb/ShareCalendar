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

// スケジュール登録
Route::post('/schedule', 'ScheduleController@create');

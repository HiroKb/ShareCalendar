<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// OAuth認証リダイレクト処理
Route::get('/o-auth/{provider}', 'CustomizedAuth\OAuthController@redirect');
// OAuth認証コールバック後処理
Route::get('/o-auth/{provider}/callback', 'CustomizedAuth\OAuthController@handleProviderCallback');

// メールアドレス認証
Route::get('/email/verify/{id}/{hash}', 'CustomizedAuth\VerificationController@verify')->name('verification.verify');

// メールアドレス変更処理
Route::get('/email/update/{token}', 'UserController@updateEmail')->middleware('auth.redirect');


Route::get('/password/reset', fn() => view('index'))->name('password.reset');

//API以外のリクエストに対してはindexビューを返す
Route::get('/{any?}', fn() => view('index'))->where('any', '.+');

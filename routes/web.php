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
Route::get('/o-auth/{provider}', 'Auth\OAuthController@redirect');
// OAuth認証コールバック後処理
Route::get('/o-auth/{provider}/callback', 'Auth\OAuthController@handleProviderCallback');

// メールアドレス認証
Route::get('/email/verify/{id}/{hash}', 'Auth\VerificationController@verify')->name('verification.verify')->middleware('test_user.redirect');

// メールアドレス変更処理
Route::get('/email/update/{token}', 'UserController@updateEmail')->middleware('auth.redirect', 'test_user.redirect');

// パスワードリセット処理用
Route::get('/password/reset', fn() => view('index'))->name('password.reset');

// 管理者
Route::namespace('Admin')->prefix('admin')->middleware(['normal_user.redirect'])->group(function (){
    Route::get(config('admin_user.path').'/login', 'LoginController@showLoginForm')->name('admin_login');
    Route::post(config('admin_user.path').'/login', 'LoginController@login');

    Route::group(['middleware' => 'admin.auth'], function () {
        Route::post(config('admin_user.path').'/logout', 'LoginController@logout')->name('admin_logout');
    });
});


//パスが存在しないリクエストに対してはindexビューを返す
Route::get('/{any?}', fn() => view('index'))->where('any', '.+');

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
Route::namespace('Admin')->prefix('admin/'.config('admin_user.path'))->middleware(['normal_user.redirect'])->group(function (){
    // ログインフォーム表示
    Route::get('login', 'LoginController@showLoginForm')->name('admin_login');
    // ログイン処理
    Route::post('login', 'LoginController@login')->middleware('record.ip');

    Route::group(['middleware' => 'admin.auth'], function () {
        // ログアウト処理
        Route::post('logout', 'LoginController@logout')->name('admin_logout');
        // 管理者ホーム画面表示
        Route::get('home', 'AdminController@index')->name('admin_index');
    });
});


//パスが存在しないリクエストに対してはindexビューを返す
Route::get('/{any?}', fn() => view('index'))->where('any', '.+');

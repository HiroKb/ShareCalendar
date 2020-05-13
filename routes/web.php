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


Route::get('/password/reset', fn() => view('index'))->name('password.reset');

//API以外のリクエストに対してはindexビューを返す
Route::get('/{any?}', fn() => view('index'))->where('any', '.+');

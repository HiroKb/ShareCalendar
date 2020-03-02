<?php

/*
|--------------------------------------------------------------------------
| InternalApi Routes
|--------------------------------------------------------------------------
*/

Route::post('/register', 'Auth\RegisterController@register')->name('register');

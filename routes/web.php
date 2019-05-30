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

Route::group(['middleware' => 'auth'], function () {
    Route::get('/', 'PagesController@dashboard')->name('dashboard');

    Route::get('userGifts/get', 'UserGiftsController@get')->name('userGifts.get');
    Route::get('userGifts/{id}/withdraw', 'UserGiftsController@withdraw')->name('userGifts.withdraw')->where('id', '[0-9]+');
    Route::get('userGifts/{id}/exchange', 'UserGiftsController@exchange')->name('userGifts.exchange')->where('id', '[0-9]+');
    Route::get('userGifts/{id}/cancel', 'UserGiftsController@cancel')->name('userGifts.cancel')->where('id', '[0-9]+');
});

// Authentication Routes...
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::get('logout', 'Auth\LoginController@logout')->name('logout');

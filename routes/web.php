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

Route::get('/', function () {
    return view('app.index');
});


Route::group(['prefix' => 'errors'], function () {
    Route::get('javascript-disabled', 'ErrorController@javascript')->name('errors.javascript-disabled');
    Route::get('cookie-disabled', 'ErrorController@cookie')->name('errors.cookie-disabled');
});
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

Route::group(['prefix' => 'auth', 'middleware' => 'guest'], function () {
    #region login
    Route::get('/login', 'Auth\LoginController@showLoginForm')->name('auth.login.get');
    Route::post('/login', 'Auth\LoginController@login')->name('auth.login.post');
    #endregion

    #region register
    Route::get('/register', 'Auth\RegisterController@showRegistrationForm')->name('auth.register.get');
    Route::post('/register', 'Auth\RegisterController@register')->name('auth.register.post');
    #endregion

    #region forgot password
    Route::get('/forgot', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('auth.password.email.get');
    #endregion

    #region reset password
    Route::get('/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('auth.password.reset.get');
    #endregion
});

Route::get('/', function () {
    return view('app.index');
});


Route::group(['prefix' => 'errors'], function () {
    Route::get('javascript-disabled', 'ErrorController@javascript')->name('errors.javascript-disabled');
    Route::get('cookie-disabled', 'ErrorController@cookie')->name('errors.cookie-disabled');
});
Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

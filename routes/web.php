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

Route::group(['prefix' => 'auth'], function () {

    Route::group(['middleware' => ['auth']], function () {
        #region logout
        Route::get('logout', 'Auth\LoginController@logout')->name('auth.logout');
        #endregion
    });

    Route::group(['middleware' => ['guest']], function () {

        #region register
        Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('auth.register.get');
        Route::post('register', 'Auth\RegisterController@register')->name('auth.register.post');
        #endregion

        #region forgot password
        Route::get('forgot', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('auth.password.email.get');
        Route::post('forgot', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('auth.password.email.post');
        #endregion

        #region reset password
        Route::get('reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('auth.password.reset.get');
        Route::post('reset', 'Auth\ResetPasswordController@reset')->name('auth.password.reset.post');
        #endregion


        #region login
        Route::get('login', 'Auth\LoginController@showLoginForm')->name('auth.login.get');
        Route::post('login', 'Auth\LoginController@login')->name('auth.login.post');

        /*third party login*/
        Route::get('{provider}', 'Auth\SocialAuthController@redirectToProvider')->name('auth.provider.redirect');
        Route::get('{provider}/callback', 'Auth\SocialAuthController@handleProviderCallback')->name('auth.provider.handle');

        #endregion
    });
});

Route::group(['middleware' => ['auth']], function () {


    Route::get('/', function () {
        return view('app.index');
    })->name('home');

    Route::group(['prefix' => 'errors'], function () {
        Route::get('javascript-disabled', 'ErrorController@javascript')->name('errors.javascript-disabled');
        Route::get('cookie-disabled', 'ErrorController@cookie')->name('errors.cookie-disabled');
    });
});



Route::get('test', 'TestController@index');
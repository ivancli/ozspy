<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*API Version 1*/
Route::group(['prefix' => 'v1', 'middleware' => ['auth:api']], function () {
    Route::get('user', 'API\Models\Auth\UsersController@index');
    Route::get('web-product', 'API\Models\Base\WebProductsController@index');
});
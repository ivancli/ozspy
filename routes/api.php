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
Route::group(['as' => 'api.', 'middleware' => ['auth:api']], function () {
    Route::group(['prefix' => 'v1', 'as' => 'v1.'], function () {
        Route::get('user', 'API\Models\Auth\UsersController@index');
        Route::get('retailer', 'API\Models\Base\RetailersController@index')->name('retailer.index');
        Route::get('retailer/{retailer}', 'API\Models\Base\RetailersController@show')->name('retailer.show');
        Route::get('web-product', 'API\Models\Base\WebProductsController@index')->name('web-product.index');
        Route::get('web-product/{webProduct}', 'API\Models\Base\WebProductsController@show')->name('web-product.show');
        Route::get('web-category', 'API\Models\Base\WebCategoriesController@index')->name('web-category.index');
        Route::get('web-category/{webCategory}', 'API\Models\Base\WebCategoriesController@show')->name('web-category.show');
    });
});
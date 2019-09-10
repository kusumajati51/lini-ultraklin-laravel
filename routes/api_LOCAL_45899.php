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
Route::post('/register', 'Api\AuthController@register');
Route::post('/login', 'Api\AuthController@login');

Route::get('/images/banners/list', 'Api\ImageController@getBannersList');
Route::get('/images/banners/{width}/{file}', function ($width, $file) {
    $request = Request::create('/images/banners/'.$width.'/'.$file, 'GET');

    return Route::dispatch($request);
});

Route::get('/services/list', 'Api\ServiceController@getList');
Route::get('/services/{name}/packages/list', 'Api\ServiceController@getPackageList');

Route::get('/packages/list', 'Api\PackageController@getList');
Route::get('/packages/{name}/items/list', 'Api\PackageController@getItemsList');

Route::get('/items/{service?}/{package?}', 'Api\ItemController@index');
Route::post('/promotions/check', 'Api\PromotionController@check');

Route::post('/password/email', 'Api\AuthController@sendResetLinkEmail');

Route::group([
    'middleware' => 'auth:api',
    'namespace' => 'Api'
], function () {
    Route::get('/invoices', 'InvoiceController@index');
    Route::get('/invoices/{code}', 'InvoiceController@show');

    Route::get('/orders', 'OrderController@index');
    Route::get('/orders/{id}', 'OrderController@show');
    Route::post('/orders', 'OrderController@store');

    Route::get('/user/profile', 'UserController@profile');

    Route::post('/fcmToken', 'UserController@storeFcmToken');

});



Route::group([
    'prefix' => 'v1',
    'namespace' => 'Api\V1'
], function () {
    Route::get('/regions/list', 'RegionController@getList');

    Route::get('/banners/list', 'BannerController@getList');

    Route::get('/packages/list', 'PackageController@getList');
    Route::get('/packages/{package}/items/list', 'PackageController@getItemList');

    Route::get('/items/list', 'ItemController@getList');

    Route::get('/menu/{name}', 'MenuController@show');
    Route::get('/menu/list', 'MenuController@getList');
});

/*
|--------------------------------------------------
| CLIENT
|--------------------------------------------------
*/
Route::group([
    'prefix' => 'v1/client',
    'namespace' => 'Api\V1'
], function () {
    Route::post('/token', 'ClientAuthController@getToken');

    Route::group(['middleware' => 'client'], function () {
        Route::get('regions/list', 'RegionController@getList');

        Route::get('/banners/list', 'BannerController@getList');

        Route::get('/packages/list', 'PackageController@getList');
        Route::get('/packages/{package}/items/list', 'PackageController@getItemList');

        Route::post('/customers/register', 'ClientCustomerController@register');

        Route::post('/orders', 'ClientOrderController@store');
    });

});


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
Route::post('/get-notification', 'Mobile\PaymentController@getNotification');
Route::post('/show-payment', 'Mobile\PaymentController@index')->middleware('auth:api');
Route::get('/payment', 'Mobile\PaymentController@show');
Route::get('/finish', 'Mobile\PaymentController@finish');
Route::post('/payment', 'Mobile\PaymentController@getPaymentChannel');
Route::post('/send-payment/{id}', 'Mobile\PaymentController@sendPayment')->middleware('auth:api');
Route::get('/get-payment-detail/{code}', 'Mobile\PaymentController@getPaymentDetail')->middleware('auth:api');


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

/*
|--------------------------------------------------
| VERSION 1
|--------------------------------------------------
*/
Route::group([
    'prefix' => 'v1',
    'namespace' => 'Api\V1'
], function () {
    Route::post('/register', 'AuthController@register');
    Route::post('/login', 'AuthController@login');

    Route::get('/regions/list', 'RegionController@getList');

    Route::get('/banners/list', 'BannerController@getList');

    Route::get('/packages/list', 'PackageController@getList');
    Route::get('/packages/{package}/items/list', 'PackageController@getItemList');

    Route::get('/items/list', 'ItemController@getList');

    Route::get('/menu/list', 'MenuController@getList');
    Route::get('/menu/{name}', 'MenuController@show');

    Route::group([
        'middleware' => 'auth:api'
    ], function () {
        Route::post('/promotions/check', 'PromotionController@check');

        Route::get('/orders', 'OrderController@index');
        Route::get('/orders/{id}', 'OrderController@show');
        Route::post('/orders', 'OrderController@store');

        Route::get('/payment/channels', 'PaymentController@getPaymentChannels');

        Route::get('/reports/agents/orders', 'AgentReportController@orders');
        Route::get('/reports/sales/orders', 'SalesReportController@orders');

        Route::post('/stores', 'StoreController@store');

        Route::group(['middleware' => 'user-store'], function () {
            Route::get('/stores/info', 'StoreController@getStore');
            Route::get('stores/nearby', 'StoreController@findNearby');
            Route::get('stores/invoices', 'InvoiceController@index');
            Route::get('stores/invoices/{id}', 'InvoiceController@getInvoice');
            Route::get('stores/orders', 'OrderController@index');
            Route::get('stores/orders/{id}', 'OrderController@find');
            Route::put('stores/orders/{id}/status', 'OrderController@updateStatus');
            Route::post('/stores/update', 'StoreController@update');

            Route::get('store/orders', 'OrderController@index');
            Route::get('store/orders/{id}', 'OrderController@find');
            Route::put('store/orders/{id}/status', 'OrderController@updateStatus');
        });
    });

    Route::post('/vote/{id}', 'RatingController@vote')->middleware('auth:api');
});

/*
|--------------------------------------------------
| VERSION 2
|--------------------------------------------------
*/
Route::group([
    'prefix' => 'v2',
    'namespace' => 'Api\V2',
    'middleware' => 'auth:api'
], function () {
    Route::post('/firebase/token', 'FirebaseController@storeToken');

    Route::get('/store/orders', 'OrderController@index');
    Route::get('/store/orders/{id}', 'OrderController@find');
    Route::put('/store/orders/{id}/status', 'OrderController@updateStatus');

    Route::get('/store/order-histories', 'StoreController@getOrderHistories');
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

        Route::post('/promotions/check', 'ClientPromotionController@check');

        Route::post('/orders', 'ClientOrderController@store');
    });
});

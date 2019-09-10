<?php

Route::post('/v1/login', '\App\Http\Controllers\Api\Officer\V1\AuthController@login');

Route::group([
    'middleware' => 'officer-auth',
    'prefix' => 'v1'
], function () {
    Route::get('/invoices', 'InvoiceController@index');
    Route::get('/logs/error', 'LogController@getErrorLogs');
});

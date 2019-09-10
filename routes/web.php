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
    if (request()->header('host') == 'admin.ultraklin.com') {
        return redirect('http://admin.ultraklin.com/admin');
    }

    return redirect('http://ultraklin.com');
});

Route::post('/user/logout', 'Auth\LoginController@logout');

Route::post('/password/reset', 'Auth\ResetPasswordController@reset');
Route::get('/password/reset/success', 'Auth\ResetPasswordController@resetSuccess');
Route::get('/password/reset/{token}','Auth\ResetPasswordController@showResetForm')->name('password.reset');

Route::get('/images/banners/{width}/{filename}', 'ImageController@getBanners');
Route::get('/images/menu-icons/{width}/{filename}', 'ImageController@getMenuIcon');
Route::get('/images/store/{width}/{filename}', 'ImageController@getStoreImage');

Route::get('/admin/login', 'Admin\Auth\LoginController@showLoginForm')
    ->middleware('guest:officer');
Route::post('/admin/login', 'Admin\Auth\LoginController@login');
Route::get('/admin/logout', 'Admin\Auth\LoginController@logout');

Route::group([
    'prefix' => 'admin',
    'namespace' => 'Admin',
    'middleware' => 'officer'
], function () {
    Route::get('/', function () {return redirect('/admin/invoices'); });

    Route::get('/dashboard', 'DashboardController@index');

    Route::get('/invoices', 'InvoiceController@index')
        ->middleware('permission:invoice__browse');
    Route::get('/invoices/{code}', 'InvoiceController@show')
        ->middleware('permission:invoice__browse');
    Route::patch('/invoices/{code}/change-status', 'InvoiceController@updateStatus')
        ->middleware('permission:invoice__change_status');
    Route::get('/invoices/{code}/print', 'InvoiceController@doPrint')
        ->middleware('permission:invoice__browse');

    Route::get('/offline-invoices', 'OfflineInvoiceController@index')
        ->middleware('permission:invoice_browse');
    Route::get('/offline-invoices/{code}', 'OfflineInvoiceController@show')
        ->middleware('permission:invoice_browse');

    Route::get('/orders', 'OrderController@index')
        ->middleware('permission:order__browse');
    Route::get('/orders/{id}', 'OrderController@show')
        ->middleware('permission:order__browse');
    Route::patch('/orders/{id}/change-status', 'OrderController@updateStatus')
        ->middleware('permission:order__change_status');

    Route::get('/offline-orders', 'OfflineOrderController@index')
        ->middleware('permission:order__browse');
    Route::get('/offline-orders/create', 'OfflineOrderController@create')
        ->middleware('permission:order__create');
    Route::get('/offline-orders/{id}', 'OfflineOrderController@show')
        ->middleware('permission:order__browse');

    Route::get('/users', 'UserController@index')
        ->middleware('permission:user__browse');

    Route::get('/customers', 'CustomerController@index')
        ->middleware('permission:customer__browse');

    Route::get('/agents', 'AgentController@index')
        ->middleware('permission:agent__browse');
    Route::get('/agents/create', 'AgentController@create')
        ->middleware('permission:agent__create');
    Route::post('/agents', 'AgentController@store')
        ->middleware('permission:agent__create');

    Route::get('/banners', 'BannerController@index')
        ->middleware('permission:banner__browse');
    Route::get('/banners/create', 'BannerController@create')
        ->middleware('permission:banner__create');
    Route::post('/banners', 'BannerController@store')
        ->middleware('permission:banner__create');
    Route::get('/banners/{id}/edit', 'BannerController@edit')
        ->middleware('permission:banner__edit');
    Route::patch('/banners/{id}', 'BannerController@update')
        ->middleware('permission:banner__edit');

    Route::get('/promotions', 'PromotionController@index')
        ->middleware('permission:promotion__browse');
    Route::get('/promotions/create', 'PromotionController@create')
        ->middleware('permission:promotion__create');
    Route::get('/promotions/{id}', 'PromotionController@show')
        ->middleware('permission:promotion__browse');
    Route::post('/promotions', 'PromotionController@store')
        ->middleware('permission:promotion__create');
    Route::get('/promotions/{id}/edit', 'PromotionController@edit')
        ->middleware('permission:promotion__edit');
    Route::patch('/promotions/{id}', 'PromotionController@update')
        ->middleware('permission:promotion__edit');

    Route::get('/services', 'ServiceController@index')
        ->middleware('permission:service__browse');
    Route::get('/services/{id}/packages', 'ServiceController@showPackages')
        ->middleware('permission:service__browse');
    
    Route::get('/packages', 'PackageController@index')
        ->middleware('permission:package__browse');
    Route::get('/packages/create', 'PackageController@create')
        ->middleware('permission:package__create');
    Route::post('/packages', 'PackageController@store')
        ->middleware('permission:package__create');
    Route::get('/packages/{id}/edit', 'PackageController@edit')
        ->middleware('permission:package__edit');
    Route::patch('/packages/{id}', 'PackageController@update')
        ->middleware('permission:package__edit');

    Route::get('/packages/{id}/items', 'PackageController@showItems')
        ->middleware('permission:package__item_browse');
    Route::get('/packages/{id}/items/add', 'PackageController@addItem')
        ->middleware('permission:package__item_create');
    Route::post('/packages/{id}/items', 'PackageController@storeItem')
        ->middleware('permission:package__item_create');
    Route::get('/packages/{id}/items/{item}/edit', 'PackageController@editItem')
        ->middleware('permission:package__item_edit');
    Route::patch('/packages/{id}/items/{item}', 'PackageController@updateItem')
        ->middleware('permission:package__item_edit');
    Route::delete('/packages/{id}/items/{item}', 'PackageController@deleteItem')
        ->middleware('permission:package__item_delete');

    Route::get('/items', 'ItemController@index')
        ->middleware('permission:item__browse');
    Route::get('/items/list', 'ItemController@getList')
        ->middleware('permission:item__browse');
    Route::get('/items/create', 'ItemController@create')
        ->middleware('permission:item__create');
    Route::post('/items', 'ItemController@store')
        ->middleware('permission:item__create');
    Route::get('/items/{id}/edit', 'ItemController@edit')
        ->middleware('permission:item__edit');
    Route::patch('/items/{id}', 'ItemController@update')
        ->middleware('permission:item__edit');

    Route::get('/permissions/json', 'PermissionController@getJson');

    Route::get('/roles', 'RoleController@index')
        ->middleware('permission:role__browse');
    Route::get('/roles/{name}/permissions', 'RoleController@showPermissions')
        ->middleware('permission:role__permission_browse');
    Route::patch('/roles/{name}/permissions', 'RoleController@updatePermissions')
        ->middleware('permission:role__permission_edit');

    Route::get('/officers', 'OfficerController@index')
        ->middleware('permission:officer__browse');
    Route::get('/officers/create', 'OfficerController@create')
        ->middleware('permission:officer__create');
    Route::post('/officers', 'OfficerController@store')
        ->middleware('permission:officer__create');
    Route::get('/officers/{id}/edit', 'OfficerController@edit')
        ->middleware('permission:officer__edit');
    Route::patch('/officers/{id}', 'OfficerController@update')
        ->middleware('permission:officer__edit');

    Route::get('/regions', 'RegionController@index');

    Route::get('/report/order', 'ReportController@getOrders')
        ->middleware('permission:report__order_browse');
    Route::get('/report/user', 'UserReportController@index')
        ->middleware('permission:report__user_browse');

    Route::get('/menu', 'MenuController@index')
        ->middleware('permission:menu__browse');
    Route::get('/menu/create', 'MenuController@create')
        ->middleware('permission:menu__create');
    Route::post('/menu', 'MenuController@store')
        ->middleware('permission:menu__create');
    Route::get('/menu/{name}', 'MenuController@show')
        ->middleware('permission:menu__browse');
    Route::get('/menu/{id}/edit', 'MenuController@edit')
        ->middleware('permission:menu__edit');
    Route::patch('/menu/{id}', 'MenuController@update')
        ->middleware('permission:menu__edit');

    Route::get('/settings', 'SettingController@index')
        ->middleware('permission:setting__browse');
    Route::patch('/settings/ajax', 'SettingController@ajaxUpdate')
        ->middleware('permission:setting__edit');

    // JSON / AJAX
    Route::get('/json/dashboard/income', 'DashboardController@jsonGetIncome');

    Route::get('/json/invoices/{code}', 'InvoiceController@jsonShow')
        ->middleware('permission:invoice__browse');

    Route::get('/json/orders/{code}', 'OrderController@jsonShow')
        ->middleware('permission:order__browse');

    Route::post('/json/offline-orders', 'OfflineOrderController@store')
        ->middleware('permission:order__create');

    Route::get('/json/users', 'UserController@jsonIndex')
        ->middleware('permission:user__browse');

    Route::get('/json/customers', 'CustomerController@jsonIndex')
        ->middleware('permission:customer__browse');
    Route::post('/json/customers', 'CustomerController@jsonStore')
        ->middleware('permission:customer__create');

    Route::patch('/json/menu/{menuId}/sort-items', 'MenuController@jsonSortItems')
        ->middleware('permission:menu__edit');
    Route::delete('/json/menu/{menuId}/items/{itemId}', 'MenuController@jsonDestroyItem')
        ->middleware('permission:menu__edit');

    Route::post('/fcmTokenOfficer', 'OfficerController@storeFcmToken');

});

Route::group([
    'prefix' => 'admin/v1',
    'namespace' => 'Admin\V1',
    'middleware' => 'officer'
], function () {
    Route::get('/', 'MainController@index');

    Route::group([
        'prefix' => 'json'
    ], function () {
        Route::get('/orders', 'OrderController@jsonIndex');
        Route::get('/orders/{code}', 'OrderController@jsonShow');
        Route::put('/orders/{code}/status', 'OrderController@updateStatus');

        Route::get('/menu', 'MenuController@jsonGetMenu');

        Route::get('/services/list', 'ServiceController@jsonGetList');

        Route::get('/agents', 'AgentController@jsonIndex');
        Route::post('/agents', 'AgentController@jsonStore');
        Route::get('/agents/{id}/edit', 'AgentController@jsonEdit');
        Route::put('/agents/{id}', 'AgentController@jsonUpdate');

        Route::get('/stores', 'StoreController@index');
        Route::put('/stores/{id}/status', 'StoreController@changeStatus');
        Route::put('/stores/{id}/packages', 'StoreController@updatePackages');

        Route::get('/sales', 'SalesController@jsonIndex');
        Route::get('/sales/{code}', 'SalesController@jsonShow');
        Route::post('/sales', 'SalesController@jsonStore');
        Route::get('/sales/{id}/edit', 'SalesController@jsonEdit');
        Route::put('/sales/{id}', 'SalesController@jsonUpdate');
        Route::get('/sales/{code}/downline', 'UserController@getUsersByUpline');
        Route::get('/sales/{code}/downline/orders', 'OrderController@getOrdersByUpline');
        Route::get('/sales/{code}/downline/orders/{orderCode}', 'OrderController@getOrderByUpline');
        
        Route::get('/agent-levels', 'AgentLevelController@jsonIndex');
        Route::post('/agent-levels', 'AgentLevelController@jsonStore');
        Route::get('/agent-levels/list', 'AgentLevelController@jsonList');
        Route::get('/agent-levels/{id}', 'AgentLevelController@jsonShow');
        Route::get('/agent-levels/{id}/edit', 'AgentLevelController@jsonEdit');
        Route::put('/agent-levels/{id}', 'AgentLevelController@jsonUpdate');

        Route::get('/sales-levels', 'SalesLevelController@jsonIndex');
        Route::post('/sales-levels', 'SalesLevelController@jsonStore');
        Route::get('/sales-levels/list', 'SalesLevelController@jsonList');
        Route::get('/sales-levels/{id}', 'SalesLevelController@jsonShow');
        Route::get('/sales-levels/{id}/edit', 'SalesLevelController@jsonEdit');
        Route::put('/sales-levels/{id}', 'SalesLevelController@jsonUpdate');

        Route::get('/widget-resources/order-count', 'WidgetOrderController@getOrderCount');
        Route::get('/widget-resources/order-status-chart', 'WidgetOrderController@getStatusPieChart');
        Route::get('/widget-resources/daily-income-chart', 'WidgetIncomeController@getDailyIncomeLineChart');

        Route::get('/u/regions', 'AdminController@getRegions');
        Route::get('/u/packages', 'AdminController@getPackages');
        Route::get('/u/regions/{id}/packages', 'AdminController@getPackagesByRegion');
    });
});

Route::group([
    'middleware' => ['officer', 'role:admin'],
    'namespace' => 'SystemTool'
], function () {
    Route::get('/system-tools', function () {
        return view('system-tools.index');
    });

    Route::put('/system-tools/json/invoices/{code}/update-price', 'InvoiceController@updatePrice');

    Route::get('/system-tools/json/logs/api', 'LoggerController@getApiLogs');
    Route::get('/system-tools/json/logs/error', 'LoggerController@getErrorLogs');
});

/*
|--------------------------------------------------------------------------
| For handle old application
|--------------------------------------------------------------------------
*/
Route::post('/Auth', 'Old\AuthController@register');
Route::post('/auth', 'Old\AuthController@register');
Route::post('/Login', 'Old\AuthController@login');
Route::post('/login', 'Old\AuthController@login');

Route::get('/Dinamic/minKilo', 'Old\ConfigController@index');
Route::get('/price/2', 'Old\ItemController@getLaundryPiece');
Route::post('/promo/beta', 'Old\PromotionController@getPromo');

Route::post('/v2/order_beta', 'Old\OrderController@storeOrder')
    ->middleware('old.api');
Route::post('/Order/byId', 'Old\OrderController@getByInvoice')
    ->middleware('old.api');
Route::get('/Order/mobile/{token}', 'Old\OrderController@index');

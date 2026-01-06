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
    return view('auth.login');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('dashboard', function () {
    return view('layouts.master');
});

Route::group(['middleware' => 'auth'], function () {
    Route::resource('categories', 'CategoryController');
    Route::get('/apiCategories', 'CategoryController@apiCategories')->name('api.categories');
    Route::get('/exportCategoriesAll', 'CategoryController@exportCategoriesAll')->name('exportPDF.categoriesAll');
    Route::get('/exportCategoriesAllExcel', 'CategoryController@exportExcel')->name('exportExcel.categoriesAll');

    Route::resource('customers', 'CustomerController');
    Route::get('/apiCustomers', 'CustomerController@apiCustomers')->name('api.customers');
    Route::post('/importCustomers', 'CustomerController@ImportExcel')->name('import.customers');
    Route::get('/exportCustomersAll', 'CustomerController@exportCustomersAll')->name('exportPDF.customersAll');
    Route::get('/exportCustomersAllExcel', 'CustomerController@exportExcel')->name('exportExcel.customersAll');

    Route::resource('suppliers', 'SupplierController');
    Route::get('/apiSuppliers', 'SupplierController@apiSuppliers')->name('api.suppliers');
    Route::post('/importSuppliers', 'SupplierController@ImportExcel')->name('import.suppliers');
    Route::get('/exportSupplierssAll', 'SupplierController@exportSuppliersAll')->name('exportPDF.suppliersAll');
    Route::get('/exportSuppliersAllExcel', 'SupplierController@exportExcel')->name('exportExcel.suppliersAll');

    Route::resource('items', 'ItemController');
    Route::get('/apiItems', 'ItemController@apiItems')->name('api.items');

    Route::resource('itemsOut', 'ItemSaleController');
    Route::get('/apiItemsOut', 'ItemSaleController@apiItemsOut')->name('api.itemsOut');
    Route::get('/exportItemSaleAll', 'ItemSaleController@exportItemSaleAll')->name('exportPDF.itemSaleAll');
    Route::get('/exportItemSaleAllExcel', 'ItemSaleController@exportExcel')->name('exportExcel.itemSaleAll');
    Route::get('/exportItemSale/{id}', 'ItemSaleController@exportItemSale')->name('exportPDF.itemSale');

    Route::resource('itemsIn', 'ItemPurchaseController');
    Route::get('/apiItemsIn', 'ItemPurchaseController@apiItemsIn')->name('api.itemsIn');
    Route::get('/exportItemPurchaseAll', 'ItemPurchaseController@exportItemPurchaseAll')->name('exportPDF.itemPurchaseAll');
    Route::get('/exportItemPurchaseAllExcel', 'ItemPurchaseController@exportExcel')->name('exportExcel.itemPurchaseAll');
    Route::get('/exportItemPurchase/{id}', 'ItemPurchaseController@exportItemPurchase')->name('exportPDF.itemPurchase');

    Route::resource('damages', 'DamageController');
    Route::get('/apiDamages', 'DamageController@apiDamages')->name('api.damages');
    Route::get('/exportDamageAll', 'DamageController@exportDamageAll')->name('exportPDF.damageAll');
    Route::get('/exportDamageAllExcel', 'DamageController@exportExcel')->name('exportExcel.damageAll');
    Route::get('/exportDamage/{id}', 'DamageController@exportDamage')->name('exportPDF.damage');

    Route::resource('user', 'UserController');
    Route::get('/apiUser', 'UserController@apiUsers')->name('api.users');
});

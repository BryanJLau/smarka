<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'WelcomeController@index');

Route::get('home', 'HomeController@index');

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);

// Admin panel
Route::get('admin', function() {
    return view('admin');
});
Route::post('admin', function() {
    return view('admin');
});

// Pay all orders
Route::post('orders/all', 'OrdersController@payAll');

Route::resource('items', 'ItemsController');
Route::post('items/changePictures', 'ItemsController@changePictures');
Route::resource('locations', 'LocationsController',
    array('only' => array('index', 'store', 'destroy')));
Route::resource('notifications', 'NotificationsController',
    array('only' => array('index', 'store')));
Route::resource('orders', 'OrdersController',
    array('only' => array('index', 'store', 'update', 'payAll')));


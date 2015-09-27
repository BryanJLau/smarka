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

// Add the admin panel for items
Route::get('items/list', 'ItemsController@listItems');
// Add the admin panel for items
Route::get('locations/list', 'LocationsController@listLocations');
// Admin panel
Route::get('admin', function() {
    return view('admin');
});
Route::post('admin', function() {
    return view('admin');
});
// Preview email
Route::get('emails/preview', function() {
    return view('emails/receipt');
});
// Pay all orders
Route::post('orders/all', 'OrdersController@payAll');

Route::resource('items', 'ItemsController');
Route::resource('notifications', 'NotificationsController',
    array('only' => array('index', 'create', 'store')));
Route::resource('orders', 'OrdersController',
    array('only' => array('index', 'create', 'store', 'update', 'payAll')));
Route::resource('locations', 'LocationsController');


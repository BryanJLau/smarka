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

Route::get('items/list', 'ItemsController@listItems');
Route::get('admin', function() {
    return view('admin');
});

Route::resource('items', 'ItemsController');
Route::resource('notifications', 'NotificationsController',
    array('only' => array('index', 'create', 'store')));
Route::resource('orders', 'OrdersController');


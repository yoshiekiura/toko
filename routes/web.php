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

Route::get('/','HomeController@index');
Route::get('/sms','HomeController@sms');




Auth::routes();

 
Route::get('/register-customer','Auth\RegisterController@register_customer'); 


Route::get('/home', 'HomeController@index')->name('home');

Route::group(['middleware' =>'auth'], function(){

	Route::resource('user', 'UserController');
	Route::resource('bank', 'BankController');
	Route::resource('komunitas', 'KomunitasController'); 
	Route::resource('warung', 'WarungController');
	Route::resource('customer', 'CustomerController');


	Route::get('user/konfirmasi/{id}',[
	'middleware' => ['auth','role:admin'],
	'as' => 'user.konfirmasi',
	'uses' => 'UserController@konfirmasi'
	]);

	Route::get('user/reset/{id}',[
	'middleware' => ['auth','role:admin'],
	'as' => 'user.reset',
	'uses' => 'UserController@reset_password'
	]);

	Route::get('user/no_konfirmasi/{id}',[
	'middleware' => ['auth'],
	'as' => 'user.no_konfirmasi',
	'uses' => 'UserController@no_konfirmasi'
	]);	


});

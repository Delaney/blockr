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

// Route::get('/', function () {
//     return view('welcome');
// });


Route::get('/start', 'TwitterController@start')->name('start');
Route::get('/cb', 'TwitterController@token')->name('token');

Route::get('/botcheck', 'TwitterController@botcheck')->name('botcheck');

Route::get('/', 'TwitterController@index')->name('main');
Route::get('/bot', 'TwitterController@index')->name('main');
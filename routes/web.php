<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', 'CountryController@index');
Route::get('/json/{c_id}', 'CountryController@getJsonData');
Route::get('/data/{c_id}', 'CountryController@getCountryInfo');
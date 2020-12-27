<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
*/

Route::get('/', 'PageController@index')->name('page.home');
Route::get('/produkte', 'PageController@products')->name('page.products');
Route::get('/damen', 'PageController@women')->name('page.women');
Route::get('/herren', 'PageController@men')->name('page.men');
Route::get('/day', 'PageController@day')->name('page.day');
Route::get('/stores', 'PageController@stores')->name('page.stores');
Route::get('/{slug?}', 'PageController@product')->name('page.product');

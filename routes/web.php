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
    return view('welcome');
});
Route::get('/admin', function () {
    return redirect('/admin/workers');
});

Route::prefix('admin/workers')->group(function () {
    Route::get('alphabetically/{section?}', 'WorkersController@alphabetically');
    Route::get('{id}', 'WorkersController@detail');
    Route::get('/', 'WorkersController@index');
});
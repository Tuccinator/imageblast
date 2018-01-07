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

Route::get('/', function() {
    return view('index');
});

Route::get('/signup', 'UserController@signup');
Route::get('/login', 'UserController@login')->name('login');
Route::post('/login', 'UserController@loginPost');
Route::get('/logout', 'UserController@logout')->middleware('auth');

Route::post('/avatar', 'UserController@uploadAvatar')->middleware('auth');
Route::get('/account', 'UserController@account')->middleware('auth');

Route::post('/upload', 'ImageController@upload')->middleware('auth');

Route::get('/groups', 'GroupController@groups');
Route::get('/groups/{id}', 'GroupController@view');
Route::get('/groups/{id}/options', 'GroupController@options')->middleware('auth');

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

Route::get('/', function () {
    return view('master');
});

Route::group(['middleware' => 'auth'], function () {
    Route::group(['middleware' => 'guest'], function () {

        Route::group(['prefix' => 'admin'], function () {
            Route::get('/','AdminController@index');
        });

        Route::group(['prefix' => 'member'], function () {
            Route::get('/','MemberController@index');
        });

    });
});

/**
 * Author resource controller
 */
Route::resource('author', 'AuthorController');

/**
 * Book resource controller
 */
Route::resource('book', 'BookController');

/**
 * User resource controller
 */
Route::resource('user', 'UserController');

/**
 * authentication routes
 */
Route::controllers([
    'auth' => 'Auth\AuthController',
    'password' => 'Auth\PasswordController',
]);
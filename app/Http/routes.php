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

Route::get('/home', ['middleware' => 'guest', function () {
    //
}]);

Route::get('/', function () {
    return view('app');
});

Route::group(['prefix' => 'admin',  'middleware' => ['auth','admin']], function()
{
    Route::get('/','AdminController@index');
    Route::get('/reports','AdminController@index');
    Route::get('/books','AdminController@books');
    Route::get('/members','AdminController@members');
});

Route::group(['prefix' => 'member',  'middleware' => ['auth','member']], function()
{
    Route::get('/','MemberController@index');
    Route::get('/home','MemberController@index');
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
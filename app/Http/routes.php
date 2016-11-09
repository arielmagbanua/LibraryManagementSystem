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
    return view('welcome');
});

Route::group(['prefix' => 'admin',  'middleware' => ['auth','admin']], function()
{
    Route::get('/','AdminController@index');
    Route::get('/reports','AdminController@index');
    Route::get('/authors','AdminController@authors');
    Route::get('/books','AdminController@books');
    Route::get('/members','AdminController@members');
    Route::get('/member_borrow_requests','AdminController@memberBorrowRequests');
    Route::get('/member_borrowed_books','AdminController@memberBorrowedBooks');

    //admin actions for a book
    Route::post('/book/{requestID}/approve_borrow_request','AdminController@approveBorrowRequest');
    Route::post('/book/{requestID}/reject_borrow_request','AdminController@rejectBorrowRequest');
    Route::post('/book/{requestID}/book_returned','AdminController@bookReturned');
    Route::post('/book/{requestID}/return_book_pending','AdminController@returnBookPending');
});

Route::group(['prefix' => 'member',  'middleware' => ['auth','member']], function()
{
    //Route::get('/','MemberController@index');
    //Route::get('/home','MemberController@index');
    Route::get('/','MemberController@books');
    Route::get('/home','MemberController@books');
    Route::get('/books','MemberController@books');
    Route::get('/borrowed_books','MemberController@borrowedBooks');
    Route::get('/borrow_request','MemberController@pendingBorrowRequest');

    //user actions for a book
    Route::post('/book/{bookID}/borrow/{startDate}','MemberController@borrow');
    Route::post('/book/{id}/return','MemberController@return');
    Route::post('/book/{requestID}/cancel_borrow_request','MemberController@cancelBorrowRequest');
});

/**
 * Server side routes
 */
Route::group(['prefix' => 'serverSide'], function()
{
    Route::get('membersList','UserController@membersList');
    Route::get('booksList','BookController@booksList');
    Route::get('authorsList','AuthorController@authorsList');
    Route::get('borrowBooksList','BookController@borrowBooksList');
    Route::get('pendingBorrowRequest','BookController@pendingBorrowRequest');
    Route::get('pendingBorrowRequestForAdmin','BookController@pendingBorrowRequestForAdmin');
    Route::get('borrowedBooksList','BookController@borrowedBooksList');
    Route::get('memberBorrowedBooksList','BookController@memberBorrowedBooksList');
    Route::get('borrowStatistics','BookController@borrowStatistics');
    Route::get('downloadBorrowReport','BookController@downloadBorrowReport');
    Route::get('downloadBooksList','BookController@downloadBooksList');
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

Route::get('test',function(){
    $books = App\Book::with('author')->get();

    return $books;
});
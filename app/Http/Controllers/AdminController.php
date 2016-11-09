<?php

namespace App\Http\Controllers;

use App\Author;
use App\Book;
use App\BorrowedBook;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use DB;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {;
        $titles = Book::orderBy('title')->lists('title','title');
        $authors = Author::select(DB::raw("CONCAT(first_name,' ',middle_name,' ',last_name) AS author_name"),'id')->orderBy('author_name')->lists('author_name','id');
        $borrowers = User::select(DB::raw("CONCAT(first_name,' ',middle_name,' ',last_name) AS borrower"),'id')->orderBy('borrower')->lists('borrower','id');
        $isbns = Book::orderBy('isbn')->lists('isbn','isbn');

        $statuses = [
            0 => 'Returned',
            1 => 'Borrowed',
            2 => 'Pending Request'
        ];

        $groupings = [
            'title' => 'Title',
            'authors.id' => 'Author',
            'books.isbn' => 'ISBN',
            'borrowed_books.status' => 'Status',
            'users.id' => 'Borrower',
        ];

        return view('admin.index',compact('titles','authors','borrowers','isbns','statuses','groupings'));
    }

    /**
     * Display books page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function books()
    {
        //get all author name and ids
        $authors = Author::select('id', DB::raw('CONCAT(first_name," ",middle_name," ",last_name) AS author_name'))
                            ->orderBy('author_name','asc')->lists('author_name','id')->toArray();

        return view('admin.books',compact('authors'));
    }

    /**
     * Display authors page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function authors()
    {
        return view('admin.authors');
    }

    /**
     * Display books page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function members()
    {
        $members = User::allMembers()->get();

        return view('admin.members',compact('members'));
    }

    /**
     * Page for member borrow requests
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function memberBorrowRequests()
    {
        return view('admin.member_borrow_requests');
    }

    /**
     * Page for member borrow books
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function memberBorrowedBooks()
    {
        return view('admin.member_borrowed_books');
    }

    public function approveBorrowRequest($requestID)
    {
        $requestedBook = BorrowedBook::where('id',$requestID)->where('status',2)->first();
        $result = $this->adminActionForBorrowRequest($requestedBook,'approve_borrow_request');
        return response()->json($result,200);
    }

    public function rejectBorrowRequest($requestID)
    {
        $requestedBook = BorrowedBook::where('id',$requestID)->where('status',2)->first();
        $result = $this->adminActionForBorrowRequest($requestedBook,'reject_borrow_request');
        return response()->json($result,200);
    }

    public function adminActionForBorrowRequest($requestedBook,$action)
    {
        $responseData = [
            'process' => 'unknown_process',
            'status' => 'fail',
            'message' => 'Unknown process'
        ];

        if($requestedBook->exists())
        {
            $responseData['process'] = $action;

            if($action=='approve_borrow_request')
            {
                $requestedBook->status = 1;
                $requestedBook->save();

                $responseData['status'] = 'success';
                $responseData['message'] = 'Book borrow request successfully approved!';
            }
            else if('reject_borrow_request')
            {
                if($requestedBook->delete())
                {
                    $responseData['status'] = 'success';
                    $responseData['message'] = 'Book borrow request successfully rejected and was removed from the system.';
                }
            }
        }

        return $responseData;
    }

    public function bookReturned($requestID)
    {
        $requestedBook = BorrowedBook::where('id',$requestID)->where('status',1)->first();
        $result = $this->adminActionForBookReturned($requestedBook,'return_book');
        return response()->json($result,200);
    }

    public function returnBookPending($requestID)
    {
        $requestedBook = BorrowedBook::where('id',$requestID)->where('status',1)->first();
        $result = $this->adminActionForBookReturned($requestedBook,'return_book_pending');
        return response()->json($result,200);
    }

    public function adminActionForBookReturned($requestedBook,$action)
    {
        $responseData = [
            'process' => 'unknown_process',
            'status' => 'fail',
            'message' => 'Unknown process'
        ];

        if($requestedBook->exists())
        {
            $responseData['process'] = $action;

            if($action=='return_book')
            {
                $requestedBook->status = 0;
                $requestedBook->date_returned = Carbon::now()->toDateString();
                $requestedBook->save();

                $responseData['status'] = 'success';
                $responseData['message'] = 'The book is successfully marked as returned!';
            }
            else if('return_book_pending')
            {
                $requestedBook->status = 2;
                $requestedBook->save();

                $responseData['status'] = 'success';
                $responseData['message'] = 'The borrowed book is successfully sent back to pending list.';
            }
        }

        return $responseData;
    }
}

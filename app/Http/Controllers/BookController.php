<?php

namespace App\Http\Controllers;

use App\Book;
use App\BorrowedBook;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests\AddBookRequest;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use DateTime;
use Log;
use Cache;
use Excel;
use PHPExcel_Style_NumberFormat;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param AddBookRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(AddBookRequest $request)
    {
        $inputs = $request->all();
        $inputs['title'] = addslashes($inputs['title']);

        $book = Book::create($inputs);

        $responseData = [
            'id' => $book->id,
            'process' => 'add_book',
            'status' => 'success'
        ];

        return response()->json($responseData,200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param AddBookRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(AddBookRequest $request, $id)
    {
        $inputs = $request->all();
        $book = Book::find($id);

        $book->title = addslashes($inputs['title']);
        $book->author_id = $inputs['author_id'];
        $book->isbn = $inputs['isbn'];
        $book->quantity = $inputs['quantity'];
        $book->overdue_fine = $inputs['overdue_fine'];
        $book->shelf_location = $inputs['shelf_location'];
        $book->save();

        $responseData = [
            'id' => $book->id,
            'status' => 'success',
            'process' => 'update_book',
            'data' => $book
        ];

        return response()->json($responseData,200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Book::destroy($id);

        return response()->json(['message' => 'Book successfully deleted!','status' => 'success'],200);
    }

    /**
     * Server side processing url for books list datatable.
     *
     * @param Request $request
     * @return array
     */
    public function booksList(Request $request)
    {
        $inputs = $request->all();
        $param = $inputs['search']['value'];

        $allBooksCount = Book::all()->count();
        $totalFiltered = $allBooksCount;

        $booksData = [];

        $booksWithLimit = Book::searchBooksWithLimit($inputs)->get();

        foreach($booksWithLimit as $book)
        {
            $editButton = '<button class="edit-book btn-actions btn btn-primary" data-toggle="modal" data-target="#book_modal_form" title="Edit" data-id="'.$book->id.'" data-action="edit_book"><span class="glyphicon glyphicon-pencil"></span></button>';
            $deleteButton = '<button class="delete-book btn-actions btn btn-danger" data-action="delete_book" data-toggle="modal" data-target="#delete_modal" title="Delete" data-id="'.$book->id.'"><span class="glyphicon glyphicon-trash"></span></button>';

            $data = [
                'title' => '<span id="book-'.$book->id.'-title">'.$book->title.'</span>',
                'author' => '<span id="book-'.$book->id.'-author" data-author="'.$book->author_id.'">'.$book->author_name.'</span>',
                'isbn' => '<span id="book-'.$book->id.'-isbn">'.$book->isbn.'</span>',
                'quantity' => '<span id="book-'.$book->id.'-quantity">'.$book->quantity.'</span>',
                'overdue_fine' => '<span id="book-'.$book->id.'-overdue_fine">'.$book->overdue_fine.'</span>',
                'shelf_location' => '<span id="book-'.$book->id.'-shelf_location">'.$book->shelf_location.'</span>',
                'actions' => $editButton.$deleteButton
            ];

            array_push($booksData,$data);
        }

        if(!empty($param) || $param!='')
        {
            $totalFiltered = Book::searchBooksWithoutLimit($inputs)->count();
        }

        $responseData = array(
            "draw"            => intval($inputs['draw']),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
            "recordsTotal"    => $allBooksCount,  // total number of records
            "recordsFiltered" => $totalFiltered, // total number of records after searching, if there is no searching then totalFiltered = totalData
            "data"            => $booksData   // total data array
        );

        return $responseData;
    }

    /**
     * Server side processing url for books list datatable that can be borrowed.
     *
     * @param Request $request
     * @return array
     */
    public function borrowBooksList(Request $request)
    {
        $inputs = $request->all();
        $param = $inputs['search']['value'];

        $allBooksCount = Book::all()->count();
        $totalFiltered = $allBooksCount;

        $booksData = [];

        $booksWithLimit = Book::searchBooksCanBeBorrowedWithLimit($inputs)->get();

        foreach($booksWithLimit as $book)
        {
            $borrowButton = '<button class="borrow-book btn-actions btn btn-success" data-toggle="modal" data-target="#borrow_book_modal" title="Borrow" data-id="'.$book->id.'" data-action="borrow_book">Borrow</button>';

            $data = [
                'title' => '<span id="book-'.$book->id.'-title">'.$book->title.'</span>',
                'author' => '<span id="book-'.$book->id.'-author" data-author="'.$book->author_id.'">'.$book->author_name.'</span>',
                'isbn' => '<span id="book-'.$book->id.'-isbn">'.$book->isbn.'</span>',
                'quantity' => '<span id="book-'.$book->id.'-quantity">'.$book->available_quantity.'</span>',
                'overdue_fine' => '<span id="book-'.$book->id.'-overdue_fine">'.$book->overdue_fine.'</span>',
                'shelf_location' => '<span id="book-'.$book->id.'-shelf_location">'.$book->shelf_location.'</span>',
                'actions' => $borrowButton
            ];

            array_push($booksData,$data);
        }

        if(!empty($param) || $param!='')
        {
            $totalFiltered = Book::searchBooksCanBeBorrowedWithoutLimit($inputs)->count();
        }

        $responseData = array(
            "draw"            => intval($inputs['draw']),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
            "recordsTotal"    => $allBooksCount,  // total number of records
            "recordsFiltered" => $totalFiltered, // total number of records after searching, if there is no searching then totalFiltered = totalData
            "data"            => $booksData   // total data array
        );

        return $responseData;
    }

    /**
     * Server side processing url for books list datatable that are pending borrow request.
     *
     * @param Request $request
     * @return array
     */
    public function pendingBorrowRequest(Request $request)
    {
        $inputs = $request->all();
        $param = $inputs['search']['value'];
        $start = $inputs['start'];
        $length = $inputs['length'];

        $columns = [
            //datatable column index  => database column name
            0 => 'title',
            1 => 'isbn',
            2 => 'overdue_fine',
            3 => 'shelf_location',
            4 => 'pivot_borrow_start_date'
        ];

        $booksData = [];

        $userID = $request->user()->id;

        $pendingRequests = User::find($userID)->borrowedBooks()->where('status',2);
        $allBooksCount = $pendingRequests->count();
        $totalFiltered = $allBooksCount;

        if(!empty($param) || $param!='')
        {
            $pendingRequests = $pendingRequests->where(function($query) use ($param){

                $query->where('title','LIKE',"%$param%")
                    ->orWhere('isbn','LIKE',"%$param%")
                    ->orWhere('shelf_location','LIKE',"%$param%");

                if(is_double($param))
                {
                    $paramDouble = doubleval($param);
                    $query->orWhere('overdue_fine','=',$paramDouble);
                }

            });
        }

        $refinedPendingRequests = $pendingRequests->withPivot('id','borrow_start_date')->orderBy($columns[$inputs['order'][0]['column']],$inputs['order'][0]['dir']);
        $booksWithLimit = $refinedPendingRequests;

        if($length>1)
        {
            $booksWithLimit->take($length)->skip($start);
        }

        $booksWithLimit = $booksWithLimit->get();

        //$booksWithLimit = Book::searchBooksCanBeBorrowedWithLimit($inputs)->get();

        foreach($booksWithLimit as $book)
        {

            $requestID = $book->pivot->id;
            $cancelButton = '<button class="borrow-book btn-actions btn btn-danger" data-toggle="modal" data-target="#cancel_request_modal" title="Cancel Borrow Request" data-id="'.$requestID.'" data-action="cancel_borrow_request">Cancel</button>';

            $data = [
                'title' => '<span id="book-'.$requestID.'-title">'.$book->title.'</span>',
                'isbn' => '<span id="book-'.$requestID.'-isbn">'.$book->isbn.'</span>',
                'overdue_fine' => '<span id="book-'.$requestID.'-overdue_fine">'.$book->overdue_fine.'</span>',
                'shelf_location' => '<span id="book-'.$requestID.'-shelf_location">'.$book->shelf_location.'</span>',
                'borrow_start_date' => '<span id="book-'.$requestID.'-borrow_start_date">'.Carbon::parse($book->pivot->borrow_start_date)->toDateString().'</span>',
                'actions' => $cancelButton
            ];

            array_push($booksData,$data);
        }

        if(!empty($param) || $param!='')
        {
            $totalFiltered = $refinedPendingRequests->count();
        }

        $responseData = array(
            "draw"            => intval($inputs['draw']),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
            "recordsTotal"    => $allBooksCount,  // total number of records
            "recordsFiltered" => $totalFiltered, // total number of records after searching, if there is no searching then totalFiltered = totalData
            "data"            => $booksData   // total data array
        );

        return $responseData;
    }

    /**
     * Server side processing url for books list datatable that are pending borrow request for admin accounts.
     *
     * @param Request $request
     * @return array
     */
    public function pendingBorrowRequestForAdmin(Request $request)
    {
        $inputs = $request->all();
        $param = $inputs['search']['value'];
        $start = $inputs['start'];
        $length = $inputs['length'];

        $columns = [
            //datatable column index  => database column name
            0 => 'books.title',
            1 => 'books.isbn',
            2 => 'books.overdue_fine',
            3 => 'user_name',
            4 => 'users.email'
        ];

        $pendingBookBorrowRequests = [];

        $pendingBorrowRequests = DB::table('borrowed_books')
                                    ->select('borrowed_books.id','books.title','books.isbn','books.overdue_fine', DB::raw("CONCAT(users.first_name,' ',users.middle_name,' ',users.last_name) AS user_name"),'users.email')
                                    ->join('users','users.id','=','borrowed_books.user_id')
                                    ->join('books','books.id','=','borrowed_books.book_id')
                                    ->where('borrowed_books.status',2);

        $allPendingBookRequestsCount = $pendingBorrowRequests->count();
        $totalFiltered = $allPendingBookRequestsCount;

        if(!empty($param) || $param!='')
        {
            $pendingBorrowRequests->where(function($query) use ($param){
                $query->where('books.title','LIKE',"%$param%")
                      ->orWhere('books.isbn','LIKE',"%$param%")
                      ->orWhere('user_name','LIKE',"%$param%")
                      ->orWhere('users.email','LIKE',"%$param%");

                if(is_double($param))
                {
                    $paramDouble = doubleval($param);
                    $query->orWhere('books.overdue_fine','=',$paramDouble);
                }
            });
        }

        $pendingBorrowRequests->orderBy($columns[$inputs['order'][0]['column']],$inputs['order'][0]['dir']);
        $requestsWithLimit = $pendingBorrowRequests;

        if($length>1)
        {
            $requestsWithLimit->take($length)->skip($start);
        }

        $requestsWithLimit = $requestsWithLimit->get();

        //$booksWithLimit = Book::searchBooksCanBeBorrowedWithLimit($inputs)->get();

        foreach($requestsWithLimit as $bookRequest)
        {

            $requestID = $bookRequest->id;
            $approveButton = '<button class="borrow-book btn-actions btn btn-success glyphicon glyphicon-thumbs-up" data-toggle="modal" data-target="#request_modal" title="Approve borrow request" data-id="'.$requestID.'" data-action="approve_borrow_request"></button>';
            $rejectButton = '<button class="borrow-book btn-actions btn btn-danger glyphicon glyphicon-thumbs-down" data-toggle="modal" data-target="#request_modal" title="Reject borrow bequest" data-id="'.$requestID.'" data-action="reject_borrow_request"></button>';

            $data = [
                'title' => '<span id="book-'.$requestID.'-title">'.$bookRequest->title.'</span>',
                'isbn' => '<span id="book-'.$requestID.'-isbn">'.$bookRequest->isbn.'</span>',
                'overdue_fine' => '<span id="book-'.$requestID.'-overdue_fine">'.$bookRequest->overdue_fine.'</span>',
                'user_name' => '<span id="book-'.$requestID.'-user_name">'.$bookRequest->user_name.'</span>',
                'email' => '<span id="book-'.$requestID.'-email">'.$bookRequest->email.'</span>',
                'actions' => $approveButton.$rejectButton
            ];

            array_push($pendingBookBorrowRequests,$data);
        }

        if(!empty($param) || $param!='')
        {
            $totalFiltered = $pendingBorrowRequests->count();
        }

        $responseData = array(
            "draw"            => intval($inputs['draw']),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
            "recordsTotal"    => $allPendingBookRequestsCount,  // total number of records
            "recordsFiltered" => $totalFiltered, // total number of records after searching, if there is no searching then totalFiltered = totalData
            "data"            => $pendingBookBorrowRequests   // total data array
        );

        return $responseData;
    }

    /**
     * Server side processing url for books list datatable that are pending borrow request for admin accounts.
     *
     * @param Request $request
     * @return array
     */
    public function borrowedBooksList(Request $request)
    {
        $inputs = $request->all();
        $userID = $request->user()->id;

        $param = $inputs['search']['value'];
        $start = $inputs['start'];
        $length = $inputs['length'];

        $columns = [
            //datatable column index  => database column name
            0 => 'books.title',
            1 => 'author_name',
            2 => 'books.isbn',
            3 => 'borrow_start_date',
            4 => 'borrowed_books.fine'
        ];

        $borrowedBooksData = [];

        $borrowedBooks = DB::table('borrowed_books')
                            ->select('borrowed_books.id','books.title','books.isbn', DB::raw("CONCAT(authors.first_name,' ',authors.middle_name,' ',authors.last_name) AS author_name"),DB::raw('DATE(borrowed_books.borrow_start_date) AS borrow_start_date'),DB::raw('DATE(ADDDATE(borrowed_books.borrow_start_date, + 14)) AS return_date'),'borrowed_books.fine')
                            ->join('books','books.id','=','borrowed_books.book_id')
                            ->join('authors','authors.id','=','books.author_id')
                            ->where('borrowed_books.status','=',1)
                            ->where('borrowed_books.user_id','=',$userID);

        $allBorrowedBooksCount = $borrowedBooks->count();
        $totalFiltered = $allBorrowedBooksCount;

        if(!empty($param) || $param!='')
        {
            $borrowedBooks->where(function($query) use ($param){

                $query->where('books.title','LIKE',"%$param%")
                    ->orWhere('books.isbn','LIKE',"%$param%")
                    ->orWhere('author_name','LIKE',"%$param%");

                if(is_double($param))
                {
                    $paramDouble = doubleval($param);
                    $query->orWhere('borrowed_books.fine','=',$paramDouble);
                }

                //for borrow_start_date
                if($this->validateDate($param))
                {
                    $query->orWhere('DATE(borrow_start_date)','=',"DATE($param)");
                    $query->orWhere('DATE(return_date)','=',"DATE($param)");
                }
            });
        }

        $borrowedBooks->orderBy($columns[$inputs['order'][0]['column']],$inputs['order'][0]['dir']);
        $borrowedBooksWithLimit = $borrowedBooks;

        if($length>1)
        {
            $borrowedBooksWithLimit->take($length)->skip($start);
        }

        $borrowedBooksWithLimit = $borrowedBooksWithLimit->get();

        foreach($borrowedBooksWithLimit as $book)
        {

            $requestID = $book->id;
            //$approveButton = '<button class="borrow-book btn-actions btn btn-success glyphicon glyphicon-thumbs-up" data-toggle="modal" data-target="#request_modal" title="Approve borrow request" data-id="'.$requestID.'" data-action="approve_borrow_request"></button>';
            //$rejectButton = '<button class="borrow-book btn-actions btn btn-danger glyphicon glyphicon-thumbs-down" data-toggle="modal" data-target="#request_modal" title="Reject borrow bequest" data-id="'.$requestID.'" data-action="reject_borrow_request"></button>';

            $data = [
                'title' => '<span id="book-'.$requestID.'-title">'.$book->title.'</span>',
                'author_name' => '<span id="book-'.$requestID.'-author_name">'.$book->author_name.'</span>',
                'isbn' => '<span id="book-'.$requestID.'-isbn">'.$book->isbn.'</span>',
                'borrow_start_date' => '<span id="book-'.$requestID.'-borrow_start_date">'.$book->borrow_start_date.'</span>',
                'return_date' => '<span id="book-'.$requestID.'-return_date">'.$book->return_date.'</span>',
                'fine' => $book->fine
            ];

            array_push($borrowedBooksData,$data);
        }

        if(!empty($param) || $param!='')
        {
            $totalFiltered = $borrowedBooks->count();
        }

        $responseData = array(
            "draw"            => intval($inputs['draw']),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
            "recordsTotal"    => $allBorrowedBooksCount,  // total number of records
            "recordsFiltered" => $totalFiltered, // total number of records after searching, if there is no searching then totalFiltered = totalData
            "data"            => $borrowedBooksData   // total data array
        );

        return $responseData;
    }

    /**
     * Server side processing url for books list datatable that are pending borrow request for admin accounts.
     *
     * @param Request $request
     * @return array
     */
    public function memberBorrowedBooksList(Request $request)
    {
        $inputs = $request->all();
        $param = $inputs['search']['value'];
        $start = $inputs['start'];
        $length = $inputs['length'];

        $columns = [
            //datatable column index  => database column name
            0 => 'books.title',
            1 => 'author_name',
            2 => 'books.isbn',
            3 => 'borrow_start_date',
            4 => 'borrowed_books.fine',
            5 => 'user_name',
            6 => 'users.email'
        ];

        $borrowedBooksData = [];

        $allBorrowedBooks = DB::table('borrowed_books')
                            ->select('borrowed_books.id','books.title',DB::raw("CONCAT(authors.first_name,' ',authors.middle_name,' ',authors.last_name) AS author_name"),'books.isbn', DB::raw('DATE(borrowed_books.borrow_start_date) AS borrow_start_date'), 'borrowed_books.fine', DB::raw("CONCAT(users.first_name,' ',users.middle_name,' ',users.last_name) AS user_name"),'users.email')
                            ->join('users','users.id','=','borrowed_books.user_id')
                            ->join('books','books.id','=','borrowed_books.book_id')
                            ->join('authors','authors.id','=','books.author_id')
                            ->where('borrowed_books.status',1);

        $allBorrowedBooksCount = $allBorrowedBooks->count();
        $totalFiltered = $allBorrowedBooksCount;

        if(!empty($param) || $param!='')
        {
            $allBorrowedBooks->where(function($query) use ($param){

                $query->where('books.title','LIKE',"%$param%")
                      ->orWhere('author_name','LIKE',"%$param%")
                      ->orWhere('books.isbn','LIKE',"%$param%")
                      ->orWhere('user_name','LIKE',"%$param%")
                      ->orWhere('users.email','LIKE',"%$param%");

                if(is_double($param))
                {
                    $paramDouble = doubleval($param);
                    $query->orWhere('borrowed_books.fine','=',$paramDouble);
                }

                //for borrow_start_date
                if($this->validateDate($param))
                {
                    $query->orWhere('DATE(borrow_start_date)','=',"DATE($param)");
                }

            });
        }

        $allBorrowedBooks->orderBy($columns[$inputs['order'][0]['column']],$inputs['order'][0]['dir']);
        $allBorrowedBooksWithLimit = $allBorrowedBooks;

        if($length>1)
        {
            $allBorrowedBooksWithLimit->take($length)->skip($start);
        }

        $allBorrowedBooksWithLimit = $allBorrowedBooksWithLimit->get();

        foreach($allBorrowedBooksWithLimit as $book)
        {

            //check if the borrow start date already started. If not then the borrow can be sent back to pending else disable the send back button
            $dateNow = Carbon::now();
            $borrowStartDate = Carbon::parse($book->borrow_start_date);

            $dateDiff = $dateNow->diffInDays($borrowStartDate,false);

            $requestID = $book->id;
            $returnButton = '<button class="borrow-book btn-actions btn btn-success glyphicon glyphicon-ok" data-toggle="modal" data-target="#request_modal" title="Click to mark this book as returned." data-member="'.$book->author_name.'" data-id="'.$requestID.'" data-action="return_book"></button>';
            $returnToPending = '<button class="borrow-book btn-actions btn btn-primary glyphicon glyphicon-repeat" data-toggle="modal" data-target="#request_modal" title="Click to return to pending request." data-id="'.$requestID.'" data-action="return_book_pending"></button>';

            if($dateDiff<0)
            {
                //this means that the borrowing already started this means this return to pending should be disabled
                $returnToPending = '<button class="borrow-book btn-actions btn btn-primary glyphicon glyphicon-repeat" data-toggle="modal" data-target="#request_modal" title="You cannot return this to pending since the borrow already started." data-id="'.$requestID.'" data-action="return_book_pending" disabled="true"></button>';
            }

            $fineTextColor = ($book->fine > 0.0) ? 'red' : 'black';

            $data = [
                'title' => '<span id="book-'.$requestID.'-title">'.$book->title.'</span>',
                'author_name' => '<span id="book-'.$requestID.'-author_name">'.$book->author_name.'</span>',
                'isbn' => '<span id="book-'.$requestID.'-isbn">'.$book->isbn.'</span>',
                'borrow_start_date' => '<span id="book-'.$requestID.'-borrow_start_date">'.$book->borrow_start_date.'</span>',
                'fine' => '<span id="book-'.$requestID.'-fine" style="color:'.$fineTextColor.'">'.$book->fine.'</span>',
                'borrower' => '<span id="book-'.$requestID.'-user_name">'.$book->user_name.'</span>',
                'borrower_email' => '<span id="book-'.$requestID.'-email">'.$book->email.'</span>',
                'actions' => $returnButton.$returnToPending
            ];

            array_push($borrowedBooksData,$data);
        }

        if(!empty($param) || $param!='')
        {
            $totalFiltered = $allBorrowedBooks->count();
        }

        $responseData = array(
            "draw"            => intval($inputs['draw']),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
            "recordsTotal"    => $allBorrowedBooksCount,  // total number of records
            "recordsFiltered" => $totalFiltered, // total number of records after searching, if there is no searching then totalFiltered = totalData
            "data"            => $borrowedBooksData   // total data array
        );

        return $responseData;
    }

    /**
     * Server side processing url for statistic for borrowed books
     *
     * @param Request $request
     * @return array
     */
    public function borrowStatistics(Request $request)
    {
        $inputs = $request->all();
        $param = $inputs['search']['value'];
        $start = $inputs['start'];
        $length = $inputs['length'];

        $columns = [
            //datatable column index  => database column name
            0 => 'books.title',
            1 => 'author_name',
            2 => 'books.isbn',
            3 => 'borrowed_books.status',
            4 => 'borrow_start_date',
            5 => 'borrowed_books.date_returned',
            6 => 'user_name',
            7 => 'borrowed_books.fine'
        ];

        $borrowedBooksData = [];

        $allBorrowedBooks = DB::table('borrowed_books')
            ->select('borrowed_books.id','users.id','books.id','books.title',DB::raw("CONCAT(authors.first_name,' ',authors.middle_name,' ',authors.last_name) AS author_name"),'books.isbn', DB::raw('DATE(borrowed_books.borrow_start_date) AS borrow_start_date'), 'borrowed_books.date_returned', 'borrowed_books.fine', DB::raw("CONCAT(users.first_name,' ',users.middle_name,' ',users.last_name) AS user_name"),'borrowed_books.status')
            ->join('users','users.id','=','borrowed_books.user_id')
            ->join('books','books.id','=','borrowed_books.book_id')
            ->join('authors','authors.id','=','books.author_id');

        $allBorrowedBooksCount = $allBorrowedBooks->count();
        $totalFiltered = $allBorrowedBooksCount;

        if(isset($inputs['title']) && !empty($inputs['title']))
        {
            $allBorrowedBooks->where('books.title','LIKE',"%".$inputs['title']."%");
        }

        if(isset($inputs['author_id']) && !empty($inputs['author_id']))
        {
            log::info('author_id: '.$inputs['author_id']);
            $allBorrowedBooks->where('authors.id','=',$inputs['author_id']);
        }

        if(isset($inputs['isbn']) && !empty($inputs['isbn']))
        {
            log::info('isbn: '.$inputs['isbn']);
            $allBorrowedBooks->where('books.isbn','LIKE',"%".$inputs['isbn']."%");
        }

        if(isset($inputs['status']) && !empty($inputs['status']))
        {
            $allBorrowedBooks->where('borrowed_books.status','=',$inputs['status']);
        }

        if(isset($inputs['user_id']) && !empty($inputs['user_id']))
        {
            $allBorrowedBooks->where('users.id','=',$inputs['user_id']);
        }

        if(!empty($param))
        {
            $allBorrowedBooks->where(function($query) use ($param){

                $query->where('books.title','LIKE',"%$param%")
                    ->orWhere('authors.first_name','LIKE',"%$param%")
                    ->orWhere('authors.middle_name','LIKE',"%$param%")
                    ->orWhere('authors.last_name','LIKE',"%$param%")
                    ->orWhere('books.isbn','LIKE',"%$param%")
                    ->orWhere('users.first_name','LIKE',"%$param%")
                    ->orWhere('users.middle_name','LIKE',"%$param%")
                    ->orWhere('users.last_name','LIKE',"%$param%");

                if(is_double($param))
                {
                    $paramDouble = doubleval($param);
                    $query->orWhere('borrowed_books.fine','=',$paramDouble);
                }

                if(is_integer($param))
                {
                    $paramDouble = intval($param);
                    $query->orWhere('borrowed_books.status','=',$paramDouble);
                }

                //for borrow_start_date
                if($this->validateDate($param))
                {
                    $query->orWhere('DATE(borrow_start_date)','=',"DATE($param)")
                            ->orWhere('DATE(borrowed_books.date_returned)','=',"DATE($param)");
                }

            });
        }

        $allBorrowedBooks->orderBy($columns[$inputs['order'][0]['column']],$inputs['order'][0]['dir']);
        $allBorrowedBooksWithLimit = $allBorrowedBooks;

        if($length>1)
        {
            $allBorrowedBooksWithLimit->take($length)->skip($start);
        }

        $allBorrowedBooksWithLimit = $allBorrowedBooksWithLimit->get();

        Cache::put('borrow_list',$allBorrowedBooksWithLimit,60);

        foreach($allBorrowedBooksWithLimit as $book)
        {

            $requestID = $book->id;

            $data = [
                'title' => '<span id="book-'.$requestID.'-title">'.$book->title.'</span>',
                'author_name' => '<span id="book-'.$requestID.'-author_name">'.$book->author_name.'</span>',
                'isbn' => '<span id="book-'.$requestID.'-isbn">'.$book->isbn.'</span>',
                'status' => '<span id="book-'.$requestID.'-status">'.$book->status.'</span>',
                'borrow_start_date' => '<span id="book-'.$requestID.'-borrow_start_date">'.$book->borrow_start_date.'</span>',
                'date_returned' => '<span id="book-'.$requestID.'-date_returned">'.$book->date_returned == '0000-00-00 00:00:00' ? '':$book->date_returned.'</span>',
                'borrower' => '<span id="book-'.$requestID.'-user_name">'.$book->user_name.'</span>',
                //'fine' => '<span id="book-'.$requestID.'-fine">'.$book->fine.'</span>'
                'fine' => $book->fine
            ];

            array_push($borrowedBooksData,$data);
        }

        if(!empty($param))
        {
            $totalFiltered = $allBorrowedBooks->count();
        }

        $responseData = array(
            "draw"            => intval($inputs['draw']),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
            "recordsTotal"    => $allBorrowedBooksCount,  // total number of records
            "recordsFiltered" => $totalFiltered, // total number of records after searching, if there is no searching then totalFiltered = totalData
            "data"            => $borrowedBooksData   // total data array
        );

        return $responseData;
    }

    public function downloadBorrowReport()
    {
        //get the cached items
        $items = Cache::get('borrow_list');

        $fileName = 'Book_Loan_Report_'.Carbon::now()->toDateString();
        $sheetTitle = $fileName;

        Excel::create($fileName,function($excel) use($fileName,$sheetTitle,$items)
        {
            $excel->sheet($sheetTitle,function($sheet) use($fileName,$items)
            {
                $rowNumber = 1;
                $firstDataRowNumber = 1;

                //Set auto size for sheet
                $sheet->setAutoSize(true);

                //style the headers
                $sheet->cells("A$rowNumber:H$rowNumber", function($cells)
                {
                    // Set font
                    $cells->setFont([
                        'size'       => '12',
                        'bold'       =>  true
                    ]);

                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                    $cells->setBackground('#337ab7');
                });

                $headers = [
                    'Title', 'Author', 'ISBN', 'Status', 'Borrow Start Date', 'Returned Date', 'Borrower', 'Fine'
                ];

                $sheet->appendRow($headers);
                ++$rowNumber;
                ++$firstDataRowNumber;

                foreach($items as $item)
                {
                    $row = [
                        $item->title,
                        $item->author_name,
                        $item->isbn,
                        $item->status,
                        $item->borrow_start_date,
                        $item->date_returned,
                        $item->user_name,
                        $item->fine
                    ];

                    //append the lead data
                    $sheet->appendRow($row);

                    ++$rowNumber;
                    //++$firstDataRowNumber;
                }

                $sheet->cell("G$rowNumber", function($cell) {

                    // manipulate the cell
                    $cell->setValue('TOTAL');
                    $cell->setAlignment('right');
                    $cell->setFont([
                        'bold'       =>  true
                    ]);

                });

                $lastRowNumber = $rowNumber - 1;
                $sheet->cell("H$rowNumber", function($cell) use($firstDataRowNumber,$lastRowNumber) {

                    // manipulate the cell
                    $cell->setValue("=SUM(H$firstDataRowNumber:H$lastRowNumber)");
                    $cell->setAlignment('right');
                    $cell->setFont([
                        'bold'       =>  true
                    ]);
                });

            });
        })->store('xls',storage_path('app'));

        $filePath = storage_path('app').'/'.$fileName.'.xls';

        if(file_exists($filePath))
        {
            return response()->download($filePath,$fileName.'.xls',[
                'Content-Length: '.filesize($filePath)
            ]);
        }
        else
        {
            exit('Requested file does not exist on our server!');
        }
    }

    public function downloadBooksList()
    {
        //get all books
        $books = Book::with('author')->get();

        $fileName = 'Books_'.Carbon::now()->toDateString();
        $sheetTitle = 'Books';

        Excel::create($fileName,function($excel) use($fileName,$sheetTitle,$books)
        {
            $excel->sheet($sheetTitle,function($sheet) use($fileName,$books)
            {
                $rowNumber = 1;
                $firstDataRowNumber = 1;

                //Set auto size for sheet
                $sheet->setAutoSize(true);

                //style the headers
                $sheet->cells("A$rowNumber:G$rowNumber", function($cells)
                {
                    // Set font
                    $cells->setFont([
                        'size'       => '12',
                        'bold'       =>  true
                    ]);

                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                    $cells->setBackground('#337ab7');
                });

                $sheet->setColumnFormat([
                    'C' => PHPExcel_Style_NumberFormat::FORMAT_TEXT
                ]);

                $headers = [
                    'Title', 'Author', 'ISBN', 'Quantity', 'Overdue Fine', 'Shelf Location', 'Date Added'
                ];

                $sheet->appendRow($headers);
                ++$rowNumber;
                ++$firstDataRowNumber;

                foreach($books as $book)
                {
                    $row = [
                        $book->title,
                        $book->author->first_name.' '.$book->author->middle_name.' '.$book->author->last_name,
                        $book->isbn,
                        $book->quantity,
                        $book->overdue_fine,
                        $book->shelf_location,
                        $book->created_at
                    ];

                    //append the lead data
                    $sheet->appendRow($row);

                    ++$rowNumber;
                    //++$firstDataRowNumber;
                }

            });
        })->store('xls',storage_path('app'));

        $filePath = storage_path('app').'/'.$fileName.'.xls';

        if(file_exists($filePath))
        {
            return response()->download($filePath,$fileName.'.xls',[
                'Content-Length: '.filesize($filePath)
            ]);
        }
        else
        {
            exit('Requested file does not exist on our server!');
        }
    }

    /**
     * Date validation
     *
     * @param $date
     * @return bool
     */
    protected function validateDate($date)
    {
        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') == $date;
    }
}

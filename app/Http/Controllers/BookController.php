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
                'user_name' => '<span id="book-'.$requestID.'-shelf_location">'.$bookRequest->user_name.'</span>',
                'email' => '<span id="book-'.$requestID.'-shelf_location">'.$bookRequest->email.'</span>',
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
                            ->select('borrowed_books.id','books.title','books.isbn', DB::raw("CONCAT(authors.first_name,' ',authors.middle_name,' ',authors.last_name) AS author_name"),DB::raw('DATE(borrowed_books.borrow_start_date) AS borrow_start_date'),'borrowed_books.fine')
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

                //for birth_date
                if($this->validateDate($param))
                {
                    $query->orWhere('DATE(borrow_start_date)','=',"DATE($param)");
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
                //'fine' => '<span id="book-'.$requestID.'-fine" style="color:'.$fineTexColor.';">'.$book->fine.'</span>'
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

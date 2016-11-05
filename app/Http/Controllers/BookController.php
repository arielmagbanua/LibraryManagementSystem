<?php

namespace App\Http\Controllers;

use App\Book;
use Illuminate\Http\Request;
use App\Http\Requests\AddBookRequest;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Log;

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

            Log::info($book);

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
}

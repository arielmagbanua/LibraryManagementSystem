<?php

namespace App\Http\Controllers;

use App\Book;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
                'author' => '<span id="book-'.$book->id.'-author">'.$book->author_name.'</span>',
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
}

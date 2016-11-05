<?php

namespace App\Http\Controllers;

use App\Author;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddAuthorRequest;

class AuthorController extends Controller
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
     * @param AddAuthorRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(AddAuthorRequest $request)
    {
        $author = Author::create($request->all());

        $responseData = [
            'id' => $author->id,
            'process' => 'add_author',
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
     * @param AddAuthorRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(AddAuthorRequest $request, $id)
    {
        $inputs = $request->all();
        $author = Author::find($id);

        $author->first_name = $inputs['first_name'];
        $author->middle_name = $inputs['middle_name'];
        $author->last_name = $inputs['last_name'];
        $author->description = $inputs['description'];
        $author->birth_date = $inputs['birth_date'];
        $author->save();

        $responseData = [
            'id' => $author->id,
            'status' => 'success',
            'process' => 'update_author',
            'data' => $author
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
        Author::destroy($id);

        return response()->json(['message' => 'Author successfully deleted!','status' => 'success'],200);
    }

    /**
     * Server side processing url for authors list datatable.
     *
     * @param Request $request
     * @return array
     */
    public function authorsList(Request $request)
    {
        $inputs = $request->all();
        $param = $inputs['search']['value'];

        $allAuthorsCount = Author::all()->count();
        $totalFiltered = $allAuthorsCount;

        $authorsData = [];

        $authorsWithLimit = Author::searchAuthorsWithLimit($inputs)->get();

        foreach($authorsWithLimit as $author)
        {
            $editButton = '<button class="edit-author btn-actions btn btn-primary" data-action="edit_author" data-toggle="modal" data-target="#author_modal_form" title="Edit" data-id="'.$author->id.'"><span class="glyphicon glyphicon-pencil"></span></button>';
            $deleteButton = '<button class="delete-author btn-actions btn btn-danger" data-action="delete_author" data-toggle="modal" data-target="#delete_modal" title="Delete" data-id="'.$author->id.'"><span class="glyphicon glyphicon-trash"></span></button>';

            $data = [
                'id' => '<span id="author-'.$author->id.'-id">'.$author->id.'</span>',
                'first_name' => '<span id="author-'.$author->id.'-first_name">'.$author->first_name.'</span>',
                'middle_name' => '<span id="author-'.$author->id.'-middle_name">'.$author->middle_name.'</span>',
                'last_name' => '<span id="author-'.$author->id.'-last_name">'.$author->last_name.'</span>',
                'description' => '<span id="author-'.$author->id.'-description">'.$author->description.'</span>',
                'birth_date' => '<span id="author-'.$author->id.'-birth_date">'.$author->birth_date.'</span>',
                'actions' => $editButton.$deleteButton
            ];

            array_push($authorsData,$data);
        }

        if(!empty($param) || $param!='')
        {
            $totalFiltered = Author::searchAuthorsWithoutLimit($inputs)->count();
        }

        $responseData = array(
            "draw"            => intval($inputs['draw']),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
            "recordsTotal"    => $allAuthorsCount,  // total number of records
            "recordsFiltered" => $totalFiltered, // total number of records after searching, if there is no searching then totalFiltered = totalData
            "data"            => $authorsData   // total data array
        );

        return $responseData;
    }
}

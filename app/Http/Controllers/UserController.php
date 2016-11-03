<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class UserController extends Controller
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
        //
    }

    /**
     * Server side processing url for members list datatable.
     * https://datatables.net/examples/ajax/objects.html
     * https://datatables.net/reference/api/row().data()
     *
     * @param Request $request
     * @return array
     */
    public function membersList(Request $request)
    {
        $inputs = $request->all();
        $param = $inputs['search']['value'];

        $allMembersCount = User::allMembers()->count();
        $totalFiltered = $allMembersCount;

        $membersData = [];

        $membersWithLimit = User::searchMembersWithLimit($inputs)->get();

        foreach($membersWithLimit as $member)
        {

            $editButton = '<button class="edit-member btn-actions btn btn-primary" title="Edit" data-id="'.$member->id.'"><span class="glyphicon glyphicon-pencil"></span></button>';
            $deleteButton = '<button class="delete-member btn-actions btn btn-danger" title="Delete" data-id="'.$member->id.'"><span class="glyphicon glyphicon-trash"></span></button>';

            $data = [
                'id' => '<span id="member-'.$member->id.'-id">'.$member->id.'</span>',
                'first_name' => '<span id="member-'.$member->id.'-first_name">'.$member->first_name.'</span>',
                'middle_name' => '<span id="member-'.$member->id.'-middle_name">'.$member->middle_name.'</span>',
                'last_name' => '<span id="member-'.$member->id.'-last_name">'.$member->last_name.'</span>',
                'address' => '<span id="member-'.$member->id.'-address">'.$member->address.'</span>',
                'email' => '<span id="member-'.$member->id.'-email">'.$member->email.'</span>',
                'birth_date' => '<span id="member-'.$member->id.'-birth_date">'.$member->birth_date.'</span>',
                'actions' => $editButton.$deleteButton
            ];

            array_push($membersData,$data);
        }

        if(!empty($param) || $param!='')
        {
            $totalFiltered = User::searchMembersWithoutLimit($inputs)->count();
        }

        $responseData = array(
            "draw"            => intval($inputs['draw']),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
            "recordsTotal"    => $allMembersCount,  // total number of records
            "recordsFiltered" => $totalFiltered, // total number of records after searching, if there is no searching then totalFiltered = totalData
            "data"            => $membersData   // total data array
        );

        return $responseData;
    }
}

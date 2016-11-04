<?php

namespace App\Http\Controllers;

use App\Author;
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
    {
        return view('admin.index');
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
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;

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
        return view('admin.books');
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

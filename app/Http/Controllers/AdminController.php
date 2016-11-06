<?php

namespace App\Http\Controllers;

use App\Author;
use App\BorrowedBook;
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

    /**
     * Page for borrow requests
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function borrowRequests()
    {
        return view('admin.borrow_requests');
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
}

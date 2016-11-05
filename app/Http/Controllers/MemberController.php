<?php

namespace App\Http\Controllers;

use App\Book;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('member.index');
    }

    /**
     * Display books page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function books()
    {
        return view('member.books');
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

    public function borrow(Request $request, $bookID)
    {
        //get the current user who will borrow.
        $user = $request->user();
        $borrowedBooks = $user->borrowedBooks();

        //check if user has already reach it borrow limit
        $userAge = Carbon::parse($user->birth_date)->diffInYears(Carbon::now(),true);
        $userBorrowCount = $borrowedBooks->where('status','<>',0)->count();

        $responseData = [
            'process' => 'borrow_book',
            'status' => 'success',
            'message' => ''
        ];

        if($userAge>12 && $userBorrowCount>=6)
        {
            $responseData['status'] = 'fail';
            $responseData['message'] = 'You are only allowed to borrow maximum of 6 books. Please return all borrowed books or cancel some borrow requests. :(';
        }
        else if($userAge<=12 && $userBorrowCount>=3)
        {
            $responseData['status'] = 'fail';
            $responseData['message'] = 'You are only allowed to borrow maximum of 3 books. Please return all borrowed books or cancel some borrow requests. :(';
        }
        else
        {
            $book = Book::find($bookID);
            $borrowedBooks->save($book);

            $responseData['status'] = 'fail';
            $responseData['message'] = 'Borrow request sent. It would be approved by a librarian soon!';
        }

        return response()->json($responseData,200);
    }
}

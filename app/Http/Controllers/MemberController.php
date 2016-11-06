<?php

namespace App\Http\Controllers;

use App\Book;
use App\BorrowedBook;
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
     * Borrowed books page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function borrowedBooks()
    {
        return view('member.borrowed_books');
    }

    /**
     * Pending borrow request page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function pendingBorrowRequest()
    {
        return view('member.pending_books');
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

    /**
     * The borrow process
     *
     * @param Request $request
     * @param $bookID
     * @return \Illuminate\Http\JsonResponse
     */
    public function borrow(Request $request, $bookID, $startDate)
    {
        //get the current user who will borrow.
        $user = $request->user();
        $borrowedBooks = $user->borrowedBooks();

        //check if user has already reach it borrow limit
        $userAge = Carbon::parse($user->birth_date)->diffInYears(Carbon::now(),true);
        $userBorrowCount = $borrowedBooks->where('status',1)->orWhere('status',2)->count();

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
            //$book = Book::find($bookID);
            //$borrowedBooks->save($book);
            $borrowedBooks->attach($bookID,['borrow_start_date' => $startDate]);

            $responseData['status'] = 'success';
            $responseData['message'] = 'Borrow request sent. It would be approved by a librarian soon!';
        }

        return response()->json($responseData,200);
    }

    public function cancelBorrowRequest(Request $request, $requestID)
    {
        $userID = $request->user()->id;

        $borrowRequest =  BorrowedBook::where('id',$requestID)->where('user_id',$userID)->first();

        $responseData = [
            'process' => 'borrow_book',
            'status' => 'success',
            'message' => ''
        ];

        if($borrowRequest->exists())
        {
            //delete the request
            $responseData['status'] = 'success';
            $responseData['message'] = 'Borrow request was successfully cancelled!';

            $borrowRequest->delete();
        }
        else
        {
            $responseData['status'] = 'fail';
            $responseData['message'] = 'Borrow request not found!';
        }

        return response()->json($responseData,200);
    }
}

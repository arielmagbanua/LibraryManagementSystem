@extends('app')

@section('header-links')
    <link rel="stylesheet" href="{{ asset('bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@endsection

@section('member-borrowed-class')
    active
@endsection

@section('member-borrowed-current')
    <span class="sr-only">(current)</span>
@endsection

@section('main-content')

    <h2>Borrowed Books</h2>
    <p>These are your currently borrowed books. Maximum duration of borrowing of books is 2 weeks, please return your borrowed books within this period.</p>
    <hr>
    <table id="borrowed_books_datatable" class="table table-hover">
        <thead>
            <tr>
                <th>Title</th>
                <th>Author</th>
                <th>ISBN</th>
                <th>Borrow Start Date</th>
                <th>Return Date</th>
                <th>Fine</th>
            </tr>
        </thead>
        <tbody></tbody>
        <tfoot>
            <tr>
                <th colspan="5" style="text-align:right; color: red;">Total:</th>
                <th style="color: red;"></th>
            </tr>
        </tfoot>
    </table>

@endsection

@section('footer-links')
    <script src="{{ asset('bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/member/borrowed-books.min.js') }}"></script>
@endsection
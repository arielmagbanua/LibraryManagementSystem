@extends('app')

@section('header-links')
@endsection

@section('member-borrowed-class')
    active
@endsection

@section('member-borrowed-current')
    <span class="sr-only">(current)</span>
@endsection

@section('main-content')

    <h2>Pending Borr </h2>
    <hr>

    <table id="pending_borrowed_books_datatable" class="table table-hover">
        <thead>
            <tr>
                <th>Title</th>
                <th>Author</th>
                <th>ISBN</th>
                <th>Available</th>
                <th>Overdue Fine</th>
                <th>Shelf Location</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

@endsection

@section('footer-links')
@endsection
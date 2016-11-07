@extends('app')

@section('header-links')
@endsection

@section('admin-reports-class')
    active
@endsection

@section('admin-reports-current')
    <span class="sr-only">(current)</span>
@endsection

@section('main-content')

    <div class="row">

        <div class="container-fluid">
            <h2>Active Book Loans/Borrows</h2>
            <hr>

            <table id="books_loans_datatable" class="table table-hover">

                <thead>
                <tr>
                    <th>Title</th>
                    <th>Author</th>
                    <th>ISBN</th>
                    <th>Shelf Location</th>
                    <th>Borrow Start Date</th>
                    <th>Fine</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

    </div>

    <div class="row">
        <div class="container-fluid">
            <h2>Active Book Loans/Borrows</h2>
            <hr>

            <table id="books_loans_datatable" class="table table-hover">
                <thead>
                <tr>
                    <th>Title</th>
                    <th>Author</th>
                    <th>ISBN</th>
                    <th>Shelf Location</th>
                    <th>Borrow Start Date</th>
                    <th>Fine</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

@endsection

@section('footer-links')
@endsection

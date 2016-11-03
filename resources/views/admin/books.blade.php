@extends('app')

@section('header-links')
    <link rel="stylesheet" href="{{ asset('bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@endsection

@section('admin-books-class')
    active
@endsection

@section('admin-books-current')
    <span class="sr-only">(current)</span>
@endsection

@section('main-content')

    <h2>Books</h2>
    <hr>

    <table id="books_datatable" class="table">
        <thead>
            <tr>
                <th>Title</th>
                <th>Author</th>
                <th>ISBN</th>
                <th>Quantity</th>
                <th>Overdue Fine</th>
                <th>Shelf Location</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

@endsection

@section('footer-links')
    <script src="{{ asset('bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
    <script>
        $(document).ready(function(){
            var baseURL = $('#baseURL').html();
            var booksListURL = baseURL+'/serverSide/booksList';

            $('#books_datatable').DataTable({
                'processing': true,
                'serverSide': true,
                'order': [[ 0, "desc" ]],
                'ajax': {
                    url: booksListURL,
                    type: 'get',
                    error: function() {}
                },
                'columns':[
                    {'data':'title'},
                    {'data':'author'},
                    {'data':'isbn'},
                    {'data':'quantity'},
                    {'data':'overdue_fine'},
                    {'data':'shelf_location'},
                    {'data':'actions','orderable': false}
                ]
            });
        });
    </script>
@endsection

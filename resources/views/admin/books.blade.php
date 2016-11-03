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

    <div class="modal fade" id="book_modal_form" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="book_modal_form_label">Modal title</h4>
                </div>
                <div class="modal-body">
                    Book modal form
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>

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
                //'dom':'lfrtip',
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

            //$("div.toolbar").html('<button>awts</button><div id="books_datatable_filter" class="dataTables_filter"><label>Search:<input type="search" class="form-control input-sm" placeholder="" aria-controls="books_datatable"></label></div>');

            $('#book_modal_form').on('show.bs.modal', function (e) {

                var invoker = $(e.relatedTarget);
                var invokerAction = invoker.data('action');

                if(invokerAction=='edit_book')
                {
                    var bookID = invoker.data('id');
                    console.log(bookID);
                }


            })
        });
    </script>
@endsection

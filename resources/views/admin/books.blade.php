@extends('app')

@section('header-links')
    <link rel="stylesheet" href="{{ asset('bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('bower_components/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('bower_components/select2-bootstrap-theme/dist/select2-bootstrap.min.css') }}">
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
                {!! Form::open(['url' => url('book/create'), 'id' => 'book_form']) !!}

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Modal title</h4>
                </div>

                <div class="modal-body">

                    <div class="form-group">
                        {!! Form::label('title', 'Title') !!}
                        {!! Form::text('title', '', ['class' => 'form-control']) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('author_id', 'Author') !!}
                        {!! Form::select('author_id', $authors, '', ['class' => 'form-control search-select', 'placeholder' => 'Select an author...', 'style' => 'width: 100%']) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('isbn', 'ISBN') !!}
                        {!! Form::text('isbn', '', ['class' => 'form-control']) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('quantity', 'Quantity') !!}
                        {!! Form::number('quantity', '', ['class' => 'form-control']) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('overdue_fine', 'Overdue Fine') !!}
                        {!! Form::number('overdue_fine', '', ['class' => 'form-control']) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('shelf_location', 'Shelf Location') !!}
                        {!! Form::text('shelf_location', '', ['class' => 'form-control']) !!}
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" id="modal_form_submit" class="btn btn-primary modal_submit">Save changes</button>
                </div>

                {!! Form::close() !!}
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
    <script src="{{ asset('bower_components/select2/dist/js/select2.min.js') }}"></script>
    <script>
        $(document).ready(function(){

            $('.search-select').select2({
                theme: 'bootstrap',
                dropdownParent: $("#book_modal_form")
            });

            var baseURL = $('#baseURL').html();
            var booksListURL = baseURL+'/serverSide/booksList';

            $('#books_datatable').DataTable({
                'processing': true,
                'serverSide': true,
                'dom':'<"row" <"col-md-6" <"row" <"col-md-4" l><"col-md-8 add_book" >>><"col-md-6" f>>rt<"row" <"col-md-6" i><"col-md-6" p>>',
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

            $("div.add_book").html('<button class="btn btn-success" data-action="add_book" data-toggle="modal" data-target="#book_modal_form">Add Book</button>');

            $('#book_modal_form').on('show.bs.modal', function (e) {

                var invoker = $(e.relatedTarget);
                var invokerAction = invoker.data('action');

                if(invokerAction=='edit_book')
                {
                    var bookID = invoker.data('id');
                    console.log(bookID);

                    //change the title of the modal
                    $('.modal-title').html('Edit Book');
                    //change the text of submit button
                    $('#modal_form_submit').html('Save Changes');
                }
                else
                {
                    //change the title of the modal
                    $('.modal-title').html('Add Book');
                    //change the text of submit button
                    $('#modal_form_submit').html('Save');

                    $('#book_form').trigger("reset");
                }

            });
        });
    </script>
@endsection

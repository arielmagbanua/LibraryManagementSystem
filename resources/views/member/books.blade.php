@extends('app')

@section('header-links')
    <link rel="stylesheet" href="{{ asset('bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
@endsection

@section('member-books-class')
    active
@endsection

@section('member-books-current')
    <span class="sr-only">(current)</span>
@endsection

@section('main-content')

    <div class="modal fade" id="borrow_book_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Borrow</h4>
                </div>

                <div class="modal-body">

                    <div class="form-group">
                        {!! Form::hidden('book_id_to_borrow', '', ['id' => 'book_id_to_borrow', 'class' => 'form-control']) !!}
                        <p id="borrow_modal_message">Borrow a copy of this book?</p>
                    </div>

                    <div class="form-group">
                        {!! Form::label('borrow_start_date', 'Borrow Start Date') !!}
                        {!! Form::text('borrow_start_date', '', ['class' => 'form-control date-field','required' => 'true']) !!}
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" id="borrow_cancel_button" data-dismiss="modal">Cancel</button>
                    <button type="button" id="borrow_button" class="btn btn-success"><span class="borrow-label">Yes</span></button>
                </div>

            </div>
        </div>
    </div>

    <h2>Books</h2>
    <hr>

    <table id="books_datatable" class="table table-hover">
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
    <script src="{{ asset('bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('js/member/books.min.js') }}"></script>
@endsection

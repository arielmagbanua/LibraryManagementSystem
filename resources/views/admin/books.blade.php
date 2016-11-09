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

                <?php
                    $formAttributes = array(
                            'url' 					=> url('book'),
                            'class'					=> 'modal_form',
                            'data-confirmation' 	=> '',
                            'data-process' 			=> 'add_book',
                            'data-parentmodal'      => '#book_modal_form',
                            'data-datatable'        => '#books_datatable',
                            'data-success'          => 'Book added successfully!',
                            'id'                    => 'book_form'

                    );
                ?>

                {!! Form::open($formAttributes) !!}

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
                        {!! Form::text('overdue_fine', '', ['class' => 'form-control']) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('shelf_location', 'Shelf Location') !!}
                        {!! Form::text('shelf_location', '', ['class' => 'form-control']) !!}
                    </div>

                    <!-- Error Container -->
                    <div class="form-group modal_error_wrapper">
                        <div class="alert alert-danger this_errors"></div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default modal-cancel-button" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary modal-form-submit-button"><span class="save-label">Save changes</span></button>
                </div>

                {!! Form::close() !!}
            </div>
        </div>
    </div>

    <!-- Delete modal -->
    @include('common.delete_modal')

    <!-- Success modal -->
    @include('common.success_modal')

    <h2>Books</h2>
    <hr>

    <table id="books_datatable" class="table table-hover">
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
    <script src="{{ asset('js/form-process.min.js') }}"></script>
    <script src="{{ asset('js/admin/books.min.js') }}"></script>
@endsection

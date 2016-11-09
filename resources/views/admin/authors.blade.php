@extends('app')

@section('header-links')
    <link rel="stylesheet" href="{{ asset('bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
@endsection

@section('admin-authors-class')
    active
@endsection

@section('admin-authors-current')
    <span class="sr-only">(current)</span>
@endsection

@section('main-content')

    <div class="modal fade" id="author_modal_form" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">

                <?php
                    $formAttributes = array(
                            'url' 					=> url('author'),
                            'class'					=> 'modal_form',
                            'data-confirmation' 	=> '',
                            'data-process' 			=> 'add_author',
                            'data-parentmodal'      => '#author_modal_form',
                            'data-datatable'        => '#authors_datatable',
                            'data-success'          => 'Author added successfully!',
                            'id'                    => 'author_form'

                    );
                ?>

                {!! Form::open($formAttributes) !!}

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Modal title</h4>
                </div>

                <div class="modal-body">

                    <div class="form-group">
                        {!! Form::label('first_name', 'First Name') !!}
                        {!! Form::text('first_name', '', ['class' => 'form-control']) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('middle_name', 'Middle Name') !!}
                        {!! Form::text('middle_name', '', ['class' => 'form-control']) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('last_name', 'Last Name') !!}
                        {!! Form::text('last_name', '', ['class' => 'form-control']) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('description', 'Description') !!}
                        {!! Form::textarea('description', '', ['class' => 'form-control', 'rows' => 2]) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('birth_date', 'Date of Birth') !!}
                        {!! Form::text('birth_date', '', ['class' => 'form-control date-field']) !!}
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

    <h2>Authors</h2>
    <hr>

    <table id="authors_datatable" class="table table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>First Name</th>
                <th>Middle Name</th>
                <th>Last Name</th>
                <th>Description</th>
                <th>Date of Birth</th>
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
    <script src="{{ asset('js/form-process.min.js') }}"></script>
    <script src="{{ asset('js/admin/authors.min.js') }}"></script>
@endsection

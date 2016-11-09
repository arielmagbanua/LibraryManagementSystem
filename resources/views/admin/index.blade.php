@extends('app')

@section('header-links')
    <link rel="stylesheet" href="{{ asset('bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('bower_components/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('bower_components/select2-bootstrap-theme/dist/select2-bootstrap.min.css') }}">
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

            <h2>Book Loans/Borrows</h2>
            <hr>

            <div id="search_parameter_container" class="panel panel-default">
                <div class="panel-body">
                    <div class="col-lg-4">
                        <div class="form-group">
                            {!! Form::label('title', 'Title') !!}
                            {!! Form::select('title', $titles, '', ['class' => 'form-control search-select', 'placeholder' => 'All', 'style' => 'width: 100%']) !!}
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="form-group">
                            {!! Form::label('author_id', 'Author') !!}
                            {!! Form::select('author_id', $authors, '', ['class' => 'form-control search-select', 'placeholder' => 'All', 'style' => 'width: 100%']) !!}
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="form-group">
                            {!! Form::label('isbn', 'ISBN') !!}
                            {!! Form::select('isbn', $isbns, '', ['class' => 'form-control search-select', 'placeholder' => 'All', 'style' => 'width: 100%']) !!}
                        </div>
                    </div>

                    <div class="col-lg-2">
                        <div class="form-group">
                            {!! Form::label('status', 'Status') !!}
                            {!! Form::select('status', $statuses, '', ['class' => 'form-control search-select', 'placeholder' => 'All', 'style' => 'width: 100%']) !!}
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="form-group">
                            {!! Form::label('user_id', 'Borrower') !!}
                            {!! Form::select('user_id', $borrowers, '', ['class' => 'form-control search-select', 'placeholder' => 'All', 'style' => 'width: 100%']) !!}
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-lg-6">
                                <button id="generate_report" class="btn btn-primary modal-form-submit-button pull-right">Generate Report</button>
                            </div>
                            <div class="col-lg-6">
                                {!! Html::link(url('serverSide/downloadBorrowReport'),'Download Report',['class' =>'btn btn-default', 'id' => 'downloadBorrowReport']) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <table id="books_loans_datatable" class="table table-hover">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>ISBN</th>
                        <th>Status</th>
                        <th>Borrow Start Date</th>
                        <th>Returned Date</th>
                        <th>Borrower</th>
                        <th>Fine</th>
                    </tr>
                </thead>
                <tbody></tbody>
                <tfoot>
                    <tr>
                        <th colspan="7" style="text-align:right; color: red;">Total:</th>
                        <th style="color: red;"></th>
                    </tr>
                </tfoot>
            </table>
        </div>

    </div>
@endsection

@section('footer-links')
    <script src="{{ asset('bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('bower_components/select2/dist/js/select2.min.js') }}"></script>
    <script src="{{ asset('js/admin/index.min.js') }}"></script>
@endsection

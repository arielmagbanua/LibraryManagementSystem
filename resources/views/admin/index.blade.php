@extends('app')

@section('header-links')
    <link rel="stylesheet" href="{{ asset('bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
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

            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="col-lg-4">
                        <div class="form-group">
                            {!! Form::label('title', 'Title') !!}
                            {!! Form::select('title', [], '', ['class' => 'form-control search-select', 'placeholder' => 'Select book title...', 'style' => 'width: 100%']) !!}
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="form-group">
                            {!! Form::label('author_id', 'Author') !!}
                            {!! Form::select('author_id', [], '', ['class' => 'form-control search-select', 'placeholder' => 'Select an author...', 'style' => 'width: 100%']) !!}
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="form-group">
                            {!! Form::label('user_id', 'Borrower') !!}
                            {!! Form::select('user_id', [], '', ['class' => 'form-control search-select', 'placeholder' => 'Select the borrower...', 'style' => 'width: 100%']) !!}
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="form-group">
                            {!! Form::label('isbn', 'ISBN') !!}
                            {!! Form::select('isbn', [], '', ['class' => 'form-control search-select', 'placeholder' => 'Select an isbn...', 'style' => 'width: 100%']) !!}
                        </div>
                    </div>

                    <div class="col-lg-2">
                        <div class="form-group">
                            {!! Form::label('shelf_location', 'Shelf Location') !!}
                            {!! Form::select('shelf_location', [], '', ['class' => 'form-control search-select', 'placeholder' => 'Select a shelf location...', 'style' => 'width: 100%']) !!}
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="form-group">
                            {!! Form::label('status', 'Status') !!}
                            {!! Form::select('status', [], '', ['class' => 'form-control search-select', 'placeholder' => 'Select an isbn...', 'style' => 'width: 100%']) !!}
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-lg-6">
                                <button class="btn btn-primary modal-form-submit-button pull-right">Generate Report</button>
                            </div>
                            <div class="col-lg-6">
                                <button class="btn btn-default modal-form-submit-button pull-left">Download Report</button>
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
                    <th>Shelf Location</th>
                    <th>Status</th>
                    <th>Borrow Start Date</th>
                    <th>Returned Date</th>
                    <th>Borrower</th>
                    <th>Fine</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

    </div>

    <!--
    <div class="row">
        <div class="container-fluid">

            <h2>Active Book Loans/Borrows</h2>
            <hr>



        </div>

        <div class="container-fluid">
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
    -->
@endsection

@section('footer-links')
    <script src="{{ asset('bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
    <script>

        var baseURL = $('#baseURL').html();
        var booksListURL = baseURL+'/serverSide/borrowStatistics';

    </script>
@endsection

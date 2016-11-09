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
    <script src="{{ asset('bower_components/select2/dist/js/select2.min.js') }}"></script>
    <script>

        $(document).ready(function()
        {
            $('.search-select').select2({
                theme: 'bootstrap',
                dropdownParent: $("#search_parameter_container")
            });

            var baseURL = $('#baseURL').html();
            var booksListURL = baseURL+'/serverSide/borrowStatistics';

            $('#books_loans_datatable').DataTable({
                'processing': true,
                'serverSide': true,
                'order': [[ 0, "desc" ]],
                'ajax': {
                    url: booksListURL,
                    type: 'get',
                    error: function() {},
                    data:function(d)
                    {
                        //get all the search parameters
                        d.title = $('#title').val();
                        d.author_id = $('#author_id').val();
                        d.isbn = $('#isbn').val();
                        d.status = $('#status').val();
                        d.user_id = $('#user_id').val();
                    }
                },
                'columns':[
                    {'data':'title'},
                    {'data':'author_name'},
                    {'data':'isbn'},
                    {'data':'status'},
                    {'data':'borrow_start_date'},
                    {'data':'date_returned'},
                    {'data':'borrower'},
                    {'data':'fine'}
                ],
                'drawCallback':function(settings)
                {
                    var api = this.api();
                    var rowCount = api.data().count();
                    
                    if(rowCount>0)
                    {
                        $('#download_borrow_report').show();
                    }

                    else
                    {
                        $('#download_borrow_report').hide();
                    }
                },
                'footerCallback':function(row, data, start, end, display)
                {
                    var api = this.api();

                    // Remove the formatting to get integer data for summation
                    var intVal = function (i) {
                        return typeof i === 'string' ?
                        i.replace(/[\$,]/g, '')*1 :
                                typeof i === 'number' ?
                                        i : 0;
                    };

                    // Total over all pages
                    var total = api
                            .column(7)
                            .data()
                            .reduce(function (a, b) {
                                return intVal(a) + intVal(b);
                            },0);

                    //Total over this page
                    var pageTotal = api
                            .column(7, { page: 'current'} )
                            .data()
                            .reduce(function (a, b) {
                                return intVal(a) + intVal(b);
                            },0);

                    // Update footer
                    $(api.column(7).footer()).html(pageTotal +' ( '+ total +' total)');
                }
            });

            $('#generate_report').click(function()
            {
                var reportsDataTable = $('#books_loans_datatable').DataTable();

                //remove the currently selected ordering
                //reportsDataTable.order([]);
                reportsDataTable.ajax.reload();
            });
        });

    </script>
@endsection

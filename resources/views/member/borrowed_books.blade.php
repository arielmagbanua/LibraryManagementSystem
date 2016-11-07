@extends('app')

@section('header-links')
    <link rel="stylesheet" href="{{ asset('bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@endsection

@section('member-borrowed-class')
    active
@endsection

@section('member-borrowed-current')
    <span class="sr-only">(current)</span>
@endsection

@section('main-content')

    <h2>Borrowed Books</h2>
    <p>These are your currently borrowed books. Maximum duration of borrowing of books is 2 weeks, please return your borrowed books within this period.</p>
    <hr>
    <table id="borrowed_books_datatable" class="table table-hover">
        <thead>
            <tr>
                <th>Title</th>
                <th>Author</th>
                <th>ISBN</th>
                <th>Borrow Start Date</th>
                <th>Return Date</th>
                <th>Fine</th>
            </tr>
        </thead>
        <tbody></tbody>
        <tfoot>
            <tr>
                <th colspan="5" style="text-align:right; color: red;">Total:</th>
                <th style="color: red;"></th>
            </tr>
        </tfoot>
    </table>

@endsection

@section('footer-links')
    <script src="{{ asset('bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>

    <script>

        var baseURL = $('#baseURL').html();
        var booksListURL = baseURL+'/serverSide/borrowedBooksList';

        $('#borrowed_books_datatable').DataTable({
            'processing': true,
            'serverSide': true,
            'order': [[ 4, "desc" ]],
            'ajax': {
                url: booksListURL,
                type: 'get',
                error: function() {}
            },
            'columns':[
                {'data':'title'},
                {'data':'author_name'},
                {'data':'isbn'},
                {'data':'borrow_start_date'},
                {'data':'return_date'},
                {'data':'fine'}
            ],
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
                        .column(5)
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        },0);

                //Total over this page
                var pageTotal = api
                        .column(5, { page: 'current'} )
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        },0);

                // Update footer
                $(api.column(5).footer()).html(pageTotal +' ( '+ total +' total)');

                //set the total to the free-slot in the header
                var freeSlotLink = $('.free-slot-link-1');
                freeSlotLink.text('Your total fine: '+total);
                freeSlotLink.css('color','red');
            }
        });
    </script>
@endsection
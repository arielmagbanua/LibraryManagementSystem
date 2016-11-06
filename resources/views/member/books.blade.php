@extends('app')

@section('header-links')
    <link rel="stylesheet" href="{{ asset('bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
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
                    {!! Form::hidden('book_id_to_borrow', '', ['id' => 'book_id_to_borrow', 'class' => 'form-control']) !!}
                    <p id="borrow_modal_message">Borrow a copy of this book?</p>
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
    <script>
        $(document).ready(function()
        {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-Token': $('meta[name="_token"]').attr('content')
                }
            });

            var baseURL = $('#baseURL').html();
            var booksListURL = baseURL+'/serverSide/borrowBooksList';

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

            $('#borrow_book_modal').on('show.bs.modal', function (e) {

                $('.modal-title').html('Borrow');

                var invoker = $(e.relatedTarget);
                var invokerAction = invoker.data('action');

                if(invokerAction=='borrow_book')
                {
                    var bookID = invoker.data('id');
                    console.log(bookID);
                    //load the id to the hidden field of the modal for deletion
                    $('#book_id_to_borrow').val(bookID);

                    //set the yes button to defaults
                    var borrowButton = $('#borrow_button');
                    var borrowLabel = borrowButton.find('span.borrow-label');
                    borrowLabel.html('Yes');
                    borrowLabel.removeClass('fa fa-spin fa-spinner');
                    borrowButton.removeAttr('disabled');
                    borrowButton.show();

                    $('#borrow_cancel_button').html('Cancel');

                    //set the body message to default
                    $('#borrow_modal_message').html('Borrow a copy of this book?');
                }

            });

            $('#borrow_button').click(function()
            {
                //get the member id
                var bookID = $('#book_id_to_borrow').val();
                console.log(bookID);

                var borrowButton = $(this);
                var borrowCancelButton = $('#borrow_cancel_button');

                //modify the delete label to show spinner load
                var borrowLabel = borrowButton.find('span.borrow-label');
                borrowLabel.html('');
                borrowLabel.addClass('fa fa-spin fa-spinner');
                borrowButton.prop('disabled',true);
                borrowCancelButton.prop('disabled',true);

                var borrowURL = baseURL + '/member/book/'+bookID+'/borrow';
                console.log(borrowURL);

                $.ajax({
                    type: 'post',
                    url: borrowURL,
                    success: function(data)
                    {
                        //remove the record in datatable
                        var table = $('#books_datatable').DataTable();

                        table.ajax.reload(function(){

                            if(data.status=='fail')
                            {
                                $('.modal-title').html('Ooppps!');
                                $('#borrow_modal_message').html(data.message);
                                borrowCancelButton.html('Ok, Understand');
                            }
                            else
                            {
                                $('.modal-title').html('Borrow');
                                $('#borrow_modal_message').html(data.message);
                                borrowCancelButton.html('Close');
                            }

                            //Change the message of modal and hide the delete button
                            borrowButton.hide();
                            borrowCancelButton.removeAttr('disabled');
                        });

                    }
                });

            });

        });

    </script>
@endsection

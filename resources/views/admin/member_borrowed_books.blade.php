@extends('app')

@section('header-links')
    <link rel="stylesheet" href="{{ asset('bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@endsection

@section('admin-borrowed-class')
    active
@endsection

@section('admin-borrowed-current')
    <span class="sr-only">(current)</span>
@endsection

@section('main-content')

    <div class="modal fade" id="request_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Cancel Borrow Request</h4>
                </div>

                <div class="modal-body">
                    {!! Form::hidden('request_id_to_return', '', ['id' => 'request_id_to_return', 'class' => 'form-control']) !!}
                    <p id="request_modal_message">Are you sure you want to cancel this book borrow request?</p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" id="cancel_button" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-default" id="yes_button"><span class="yes-label">Yes</span></button>
                </div>

            </div>
        </div>
    </div>

    <h2>Borrowed Books</h2>
    <br>
    <p>These are the borrowed books by the members. Here you can mark the books as returned if the member has indeed returned the books.</p>
    <hr>

    <table id="pending_borrowed_books_datatable" class="table table-hover">
        <thead>
        <tr>
            <th>Title</th>
            <th>Author</th>
            <th>ISBN</th>
            <th>Borrow Start Date</th>
            <th>Fine</th>
            <th>Borrower</th>
            <th>Borrower Email</th>
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
            var booksListURL = baseURL+'/serverSide/memberBorrowedBooksList';

            $('#pending_borrowed_books_datatable').DataTable({
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
                    {'data':'author_name'},
                    {'data':'isbn'},
                    {'data':'borrow_start_date'},
                    {'data':'fine'},
                    {'data':'borrower'},
                    {'data':'borrower_email'},
                    {'data':'actions','orderable': false}
                ]
            });

            $('#request_modal').on('show.bs.modal', function (e) {

                var invoker = $(e.relatedTarget);
                var invokerAction = invoker.data('action');

                var requestID = invoker.data('id');
                var memberName = invoker.data('member');
                console.log(requestID);
                console.log(memberName);

                var yesButton = $('#yes_button');
                var yesLabel = yesButton.find('span.yes-label');

                if(invokerAction=='return_book')
                {
                    $('.modal-title').html('Return Book');

                    yesButton.data('action','return_book');

                    //load the id to the hidden field of the modal for deletion
                    $('#request_id_to_return').val(requestID);

                    //set the yes button to defaults
                    yesButton.removeClass('btn-default');
                    yesButton.removeClass('btn-danger');
                    yesButton.addClass('btn-success');
                    yesButton.removeAttr('disabled');
                    yesButton.show();

                    yesLabel.html('Yes');
                    yesLabel.removeClass('fa fa-spin fa-spinner');

                    $('#cancel_button').html('Cancel');

                    //set the body message to default
                    $('#request_modal_message').html('Are you sure that '+memberName+' already returned this book?');
                }

            });

            $('#yes_button').click(function()
            {
                //get the action of the button
                var yesButton = $(this);
                var yesAction = yesButton.data('action');
                var yesLabel = yesButton.find('span.yes-label');
                yesLabel.html('');
                yesLabel.addClass('fa fa-spin fa-spinner');

                var cancelButton = $('#cancel_button');

                //get the request id
                var requestID = $('#request_id_to_return').val();
                console.log(requestID);
                console.log(yesAction);

                //modify the delete label to show spinner load
                yesButton.prop('disabled',true);
                cancelButton.prop('disabled',true);

                var actionURL = '';

                if(yesAction==='return_book')
                {
                    actionURL = baseURL + '/admin/book/'+requestID+'/book_returned';
                }
                else if(yesAction==='return_pending')
                {
                    //actionURL = baseURL + '/admin/book/'+requestID+'/reject_borrow_request';
                }

                console.log(actionURL);

                $.ajax({
                    type: 'post',
                    url: actionURL,
                    success: function(data)
                    {
                        //remove the record in datatable
                        var table = $('#pending_borrowed_books_datatable').DataTable();

                        table.ajax.reload(function(){

                            if(data.status=='fail')
                            {
                                $('.modal-title').html('Ooppps!');
                                $('#request_modal_message').html(data.message);
                                cancelButton.html('Ok, I Understand');
                            }
                            else
                            {
                                $('.modal-title').html('Success');
                                $('#request_modal_message').html(data.message);
                                cancelButton.html('Close');
                            }

                            //Change the message of modal and hide the delete button
                            yesButton.hide();
                            cancelButton.removeAttr('disabled');

                        },false);

                    }
                });

            });

        });

    </script>
@endsection
@extends('app')

@section('header-links')
    <link rel="stylesheet" href="{{ asset('bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@endsection

@section('member-pending-borrow-class')
    active
@endsection

@section('member-pending-borrow-current')
    <span class="sr-only">(current)</span>
@endsection

@section('main-content')

    <div class="modal fade" id="cancel_request_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Cancel Borrow Request</h4>
                </div>

                <div class="modal-body">
                    {!! Form::hidden('request_id_to_cancel', '', ['id' => 'request_id_to_cancel', 'class' => 'form-control']) !!}
                    <p id="cancel_request_modal_message">Are you sure you want to cancel this book borrow request?</p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" id="cancel_borrow_request_button" data-dismiss="modal">Cancel</button>
                    <button type="button" id="yes_cancel_borrow_request_button" class="btn btn-danger"><span class="yes-label">Yes</span></button>
                </div>

            </div>
        </div>
    </div>

    <h2>Pending Borrow Requests</h2>
    <br>
    <p>These are your pending borrow request and they are not yet approved by a librarian.</p>
    <hr>

    <table id="pending_borrowed_books_datatable" class="table table-hover">
        <thead>
            <tr>
                <th>Title</th>
                <th>ISBN</th>
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
            var booksListURL = baseURL+'/serverSide/pendingBorrowRequest';

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
                    {'data':'isbn'},
                    {'data':'overdue_fine'},
                    {'data':'shelf_location'},
                    {'data':'actions','orderable': false}
                ]
            });

            $('#cancel_request_modal').on('show.bs.modal', function (e) {

                $('.modal-title').html('Cancel Borrow Request');

                var invoker = $(e.relatedTarget);
                var invokerAction = invoker.data('action');

                if(invokerAction=='cancel_borrow_request')
                {
                    var requestID = invoker.data('id');
                    console.log(requestID);
                    //load the id to the hidden field of the modal for deletion
                    $('#request_id_to_cancel').val(requestID);

                    //set the yes button to defaults
                    var yesCanCelButton = $('#yes_cancel_borrow_request_button');
                    var yesLabel = yesCanCelButton.find('span.yes-label');
                    yesLabel.html('Yes');
                    yesLabel.removeClass('fa fa-spin fa-spinner');
                    yesCanCelButton.removeAttr('disabled');
                    yesCanCelButton.show();

                    $('#cancel_borrow_request_button').html('No');

                    //set the body message to default
                    $('#cancel_request_modal_message').html('Are you sure you want to cancel this book borrow request?');
                }

            });

            $('#yes_cancel_borrow_request_button').click(function()
            {
                //get the member id
                var requestID = $('#request_id_to_cancel').val();
                console.log(requestID);

                var yesCancelButton = $(this);
                var cancelButton = $('#cancel_borrow_request_button');

                //modify the delete label to show spinner load
                var yesLabel = yesCancelButton.find('span.yes-label');
                yesLabel.html('');
                yesLabel.addClass('fa fa-spin fa-spinner');
                yesCancelButton.prop('disabled',true);
                cancelButton.prop('disabled',true);

                var cancelBorrowRequestURL = baseURL + '/member/book/'+requestID+'/cancel_borrow_request';
                console.log(cancelBorrowRequestURL);

                $.ajax({
                    type: 'post',
                    url: cancelBorrowRequestURL,
                    success: function(data)
                    {
                        //remove the record in datatable
                        var table = $('#pending_borrowed_books_datatable').DataTable();

                        table.ajax.reload(function(){

                            if(data.status=='fail')
                            {
                                $('.modal-title').html('Ooppps!');
                                $('#cancel_request_modal_message').html(data.message);
                                cancelButton.html('Ok, Understand');
                            }
                            else
                            {
                                $('.modal-title').html('Cancel Borrow Request');
                                $('#cancel_request_modal_message').html(data.message);
                                cancelButton.html('Close');
                            }

                            //Change the message of modal and hide the delete button
                            yesCancelButton.hide();
                            cancelButton.removeAttr('disabled');
                        });

                    }
                });

            });
        });

    </script>
@endsection
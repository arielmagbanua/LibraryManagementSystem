$(document).ready(function()
{
    $.ajaxSetup({
        headers: {
            'X-CSRF-Token': $('meta[name="_token"]').attr('content')
        }
    });

    var baseURL = $('#baseURL').html();
    var booksListURL = baseURL+'/serverSide/pendingBorrowRequestForAdmin';

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
            {'data':'user_name'},
            {'data':'email'},
            {'data':'actions','orderable': false}
        ]
    });

    $('#request_modal').on('show.bs.modal', function (e) {

        var invoker = $(e.relatedTarget);
        var invokerAction = invoker.data('action');

        var requestID = invoker.data('id');
        console.log(requestID);

        var yesButton = $('#yes_button');
        var yesLabel = yesButton.find('span.yes-label');

        if(invokerAction=='approve_borrow_request')
        {
            $('.modal-title').html('Approve Borrow Request');

            yesButton.data('action','approve_borrow_request');

            //load the id to the hidden field of the modal for deletion
            $('#request_id_to_cancel_approve').val(requestID);

            //set the yes button to defaults
            yesButton.removeClass('btn-default');
            yesButton.removeClass('btn-danger');
            yesButton.addClass('btn-success');
            yesButton.removeAttr('disabled');
            yesButton.show();

            yesLabel.html('Yes Approve!');
            yesLabel.removeClass('fa fa-spin fa-spinner');

            $('#cancel_button').html('No');

            //set the body message to default
            $('#request_modal_message').html('Are you sure you want to approve this book borrow request?');
        }
        else if(invokerAction=='reject_borrow_request')
        {
            $('.modal-title').html('Reject Borrow Request');

            yesButton.data('action','reject_borrow_request');

            //load the id to the hidden field of the modal for deletion
            $('#request_id_to_cancel_approve').val(requestID);

            //set the yes button to defaults
            yesButton.removeClass('btn-default');
            yesButton.removeClass('btn-success');
            yesButton.addClass('btn-danger');
            yesButton.removeAttr('disabled');
            yesButton.show();

            yesLabel.html('Yes Reject!');
            yesLabel.removeClass('fa fa-spin fa-spinner');

            $('#cancel_button').html('No');

            //set the body message to default
            $('#request_modal_message').html('Are you sure you want to reject this book borrow request?');
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
        var requestID = $('#request_id_to_cancel_approve').val();
        console.log(requestID);
        console.log(yesAction);

        //modify the delete label to show spinner load
        yesButton.prop('disabled',true);
        cancelButton.prop('disabled',true);

        var actionURL = '';

        if(yesAction==='approve_borrow_request')
        {
            actionURL = baseURL + '/admin/book/'+requestID+'/approve_borrow_request';
        }
        else if(yesAction==='reject_borrow_request')
        {
            actionURL = baseURL + '/admin/book/'+requestID+'/reject_borrow_request';
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
                        cancelButton.html('Ok, Understand');
                    }
                    else
                    {
                        $('.modal-title').html('Cancel Borrow Request');
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
$(document).ready(function()
{
    $.ajaxSetup({
        headers: {
            'X-CSRF-Token': $('meta[name="_token"]').attr('content')
        }
    });

    var date = new Date();
    date.setDate(date.getDate()-1);

    $('#borrow_start_date').datepicker({
        format: "yyyy-mm-dd",
        clearBtn: true,
        autoclose: true,
        todayHighlight: true,
        startDate: date
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

        if(e.namespace === 'bs.modal')
        {
            $('.modal-title').html('Borrow');
            $('#borrow_start_date').val('');

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
        }

    });

    $('#borrow_button').click(function()
    {
        //get the member id
        var bookID = $('#book_id_to_borrow').val();
        console.log(bookID);
        var startDate = $('#borrow_start_date').val();
        console.log(startDate);

        //do not let the user proceed if there is not start date
        if(startDate=='' || startDate==undefined)
        {
            $('#borrow_modal_message').html('Please provide the borrow start date!');
            return;
        }

        var borrowButton = $(this);
        var borrowCancelButton = $('#borrow_cancel_button');

        //modify the delete label to show spinner load
        var borrowLabel = borrowButton.find('span.borrow-label');
        borrowLabel.html('');
        borrowLabel.addClass('fa fa-spin fa-spinner');
        borrowButton.prop('disabled',true);
        borrowCancelButton.prop('disabled',true);

        var borrowURL = baseURL + '/member/book/'+bookID+'/borrow/'+startDate;
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

                },false);

            }
        });

    });

});
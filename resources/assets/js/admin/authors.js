$(document).ready(function()
{
    $('#birth_date').datepicker({
        format: "yyyy-mm-dd",
        clearBtn: true,
        autoclose: true,
        defaultViewDate: { year: 1998, month: 00, day: 01 }
    });

    var baseURL = $('#baseURL').html();
    var authorsListURL = baseURL+'/serverSide/authorsList';

    $('#authors_datatable').DataTable({
        'processing': true,
        'serverSide': true,
        'dom':'<"row" <"col-md-6" <"row" <"col-md-4" l><"col-md-8 add_author" >>><"col-md-6" f>>rt<"row" <"col-md-6" i><"col-md-6" p>>',
        'order': [[ 0, "asc" ]],
        'ajax': {
            url: authorsListURL,
            type: 'get',
            error: function() {}
        },
        'columns':[
            {'data':'id'},
            {'data':'first_name'},
            {'data':'middle_name'},
            {'data':'last_name'},
            {'data':'description'},
            {'data':'birth_date'},
            {'data':'actions','orderable': false}
        ]
    });

    $("div.add_author").html('<button class="btn btn-success" data-action="add_author" data-toggle="modal" data-target="#author_modal_form">Add Author</button>');

    $('#author_modal_form').on('show.bs.modal', function (e) {

        if(e.namespace === 'bs.modal')
        {

            var invoker = $(e.relatedTarget);
            var invokerAction = invoker.data('action');

            var authorForm = $('#author_form');

            if(invokerAction=='edit_author')
            {
                var authorID = invoker.data('id');
                console.log(authorID);

                //change the title of the modal
                $('.modal-title').html('Edit Author');
                //change the text of submit button
                $('.save-label').html('Save Changes');

                authorForm.attr('action', baseURL+'/author/'+authorID);
                authorForm.attr('method', 'PATCH');
                authorForm.data('success','Author updated successfully!');

                //row base ID
                var rowBaseID = '#author-'+authorID+'-';

                //get all the data from row
                var firstName = $(rowBaseID+'first_name').html();
                var middleName = $(rowBaseID+'middle_name').html();
                var lastName = $(rowBaseID+'last_name').html();
                var description = $(rowBaseID+'description').html();
                var birthDate = $(rowBaseID+'birth_date').html();

                //load the data in the form
                var modal = $('#author_modal_form');
                modal.find('#first_name').val(firstName);
                modal.find('#middle_name').val(middleName);
                modal.find('#last_name').val(lastName);
                modal.find('#description').val(description);
                modal.find('#birth_date').val(birthDate);
            }
            else
            {
                $('#author_modal_form :input').val('');

                //change the title of the modal
                $('.modal-title').html('Add Author');
                //change the text of submit button
                $('.save-label').html('Save');

                authorForm.attr('action', baseURL+'/author');
                authorForm.attr('method', 'POST');
                authorForm.data('success','Author added successfully!');
            }

        }

    });

    $('#delete_modal').on('show.bs.modal', function (e) {

        var invoker = $(e.relatedTarget);
        var invokerAction = invoker.data('action');

        if(invokerAction=='delete_author')
        {
            $('.modal-title').html('Delete Author');

            var authorID = invoker.data('id');
            console.log(authorID);
            //load the id to the hidden field of the modal for deletion
            $('#id_to_delete').val(authorID);

            //set the yes button to defaults
            var deleteButton = $('#delete_button');
            var deleteLabel = deleteButton.find('span.delete-label');
            deleteLabel.html('Yes Delete');
            deleteLabel.removeClass('fa fa-spin fa-spinner');
            deleteButton.removeAttr('disabled');
            deleteButton.show();
            $('#delete_cancel_button').html('Cancel');

            //set the body message to default
            $('#delete_modal_message').html('Are you sure you want to delete this author?');
        }

    });

    $('#delete_button').click(function()
    {
        //get the member id
        var authorID = $('#id_to_delete').val();
        console.log(authorID);

        var deleteButton = $(this);
        var deleteCancelButton = $('#delete_cancel_button');

        //modify the delete label to show spinner load
        var deleteLabel = deleteButton.find('span.delete-label');
        deleteLabel.html('');
        deleteLabel.addClass('fa fa-spin fa-spinner');
        deleteButton.prop('disabled',true);
        deleteCancelButton.prop('disabled',true);

        var deleteURL = baseURL + '/author/'+authorID;
        console.log(deleteURL);

        $.ajax({
            type: 'DELETE',
            url: deleteURL,
            success: function()
            {
                //remove the record in datatable
                var table = $('#authors_datatable').DataTable();
                //var row = $('#author-'+authorID+'-first_name').parents('tr');
                //table.row(row).remove().draw();

                table.ajax.reload(function(){

                    //Change the message of modal and hide the delete button
                    $('#delete_modal_message').html('The book was successfully deleted!');
                    deleteButton.hide();
                    deleteCancelButton.html('Close');
                    deleteCancelButton.removeAttr('disabled');

                },false);
            }
        });

    });

});
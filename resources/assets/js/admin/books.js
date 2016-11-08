$(document).ready(function(){

    var books = '';

    $('.search-select').select2({
        theme: 'bootstrap',
        dropdownParent: $("#book_modal_form")
    });

    var baseURL = $('#baseURL').html();
    var booksListURL = baseURL+'/serverSide/booksList';

    $('#books_datatable').DataTable({
        'processing': true,
        'serverSide': true,
        'dom':'<"row" <"col-md-6" <"row" <"col-md-4" l><"col-md-8 add_book" >>><"col-md-6" f>>rt<"row" <"col-md-6" i><"col-md-6" p>>',
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

    $("div.add_book").html('<button class="btn btn-success" data-action="add_book" data-toggle="modal" data-target="#book_modal_form">Add Book</button>');

    $('#book_modal_form').on('show.bs.modal', function (e) {

        if(e.namespace === 'bs.modal')
        {

            var invoker = $(e.relatedTarget);
            var invokerAction = invoker.data('action');

            var bookForm = $('#book_form');

            if(invokerAction=='edit_book')
            {
                var bookID = invoker.data('id');
                console.log(bookID);

                //change the title of the modal
                $('.modal-title').html('Edit Book');
                //change the text of submit button
                $('.save-label').html('Save Changes');

                bookForm.attr('action', baseURL+'/book/'+bookID);
                bookForm.attr('method', 'PATCH');
                bookForm.data('success','Book updated successfully!');

                //row base ID
                var rowBaseID = '#book-'+bookID+'-';
                var title = $(rowBaseID+'title').html();
                var authorID = $(rowBaseID+'author').data('author');
                var isbn = $(rowBaseID+'isbn').html();
                var quantity = $(rowBaseID+'quantity').html();
                var overdueFine = $(rowBaseID+'overdue_fine').html();
                var shelfLocation = $(rowBaseID+'shelf_location').html();

                console.log(authorID);

                //load the data in the form
                var modal = $('#book_modal_form');
                modal.find('#title').val(title);
                modal.find('#author_id').val(authorID).trigger('change');
                modal.find('#isbn').val(isbn);
                modal.find('#quantity').val(quantity);
                modal.find('#overdue_fine').val(overdueFine);
                modal.find('#shelf_location').val(shelfLocation);
            }
            else
            {
                $('#book_modal_form :input').val('');

                //change the title of the modal
                $('.modal-title').html('Add Book');
                //change the text of submit button
                $('.save-label').html('Save');

                bookForm.attr('action', baseURL+'/book');
                bookForm.attr('method', 'POST');
                bookForm.data('success','Book added successfully!');
            }

        }

    });

    $('#delete_modal').on('show.bs.modal', function (e) {

        var invoker = $(e.relatedTarget);
        var invokerAction = invoker.data('action');

        if(invokerAction=='delete_book')
        {
            $('.modal-title').html('Delete Book');

            var bookID = invoker.data('id');
            console.log(bookID);
            //load the id to the hidden field of the modal for deletion
            $('#id_to_delete').val(bookID);

            //set the yes button to defaults
            var deleteButton = $('#delete_button');
            var deleteLabel = deleteButton.find('span.delete-label');
            deleteLabel.html('Yes Delete');
            deleteLabel.removeClass('fa fa-spin fa-spinner');
            deleteButton.removeAttr('disabled');
            deleteButton.show();
            $('#delete_cancel_button').html('Cancel');

            //set the body message to default
            $('#delete_modal_message').html('Are you sure you want to delete this book?');
        }

    });

    $('#delete_button').click(function()
    {
        //get the member id
        var bookID = $('#id_to_delete').val();
        console.log(bookID);

        var deleteButton = $(this);
        var deleteCancelButton = $('#delete_cancel_button');

        //modify the delete label to show spinner load
        var deleteLabel = deleteButton.find('span.delete-label');
        deleteLabel.html('');
        deleteLabel.addClass('fa fa-spin fa-spinner');
        deleteButton.prop('disabled',true);
        deleteCancelButton.prop('disabled',true);

        var deleteURL = baseURL + '/book/'+bookID;
        console.log(deleteURL);

        $.ajax({
            type: 'DELETE',
            url: deleteURL,
            success: function()
            {
                //remove the record in datatable
                var table = $('#books_datatable').DataTable();
                //var row = $('#book-'+bookID+'-title').parents('tr');
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

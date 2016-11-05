@extends('app')

@section('header-links')
    <link rel="stylesheet" href="{{ asset('bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
@endsection

@section('admin-authors-class')
    active
@endsection

@section('admin-authors-current')
    <span class="sr-only">(current)</span>
@endsection

@section('main-content')

    <div class="modal fade" id="author_modal_form" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">

                <?php
                    $formAttributes = array(
                            'url' 					=> url('author'),
                            'class'					=> 'modal_form',
                            'data-confirmation' 	=> '',
                            'data-process' 			=> 'add_author',
                            'data-parentmodal'      => '#author_modal_form',
                            'data-datatable'        => '#authors_datatable',
                            'data-success'          => 'Author added successfully!',
                            'id'                    => 'author_form'

                    );
                ?>

                {!! Form::open($formAttributes) !!}

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Modal title</h4>
                </div>

                <div class="modal-body">

                    <div class="form-group">
                        {!! Form::label('first_name', 'First Name') !!}
                        {!! Form::text('first_name', '', ['class' => 'form-control']) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('middle_name', 'Middle Name') !!}
                        {!! Form::text('middle_name', '', ['class' => 'form-control']) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('last_name', 'Last Name') !!}
                        {!! Form::text('last_name', '', ['class' => 'form-control']) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('description', 'Description') !!}
                        {!! Form::textarea('description', '', ['class' => 'form-control', 'rows' => 2]) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('birth_date', 'Date of Birth') !!}
                        {!! Form::text('birth_date', '', ['class' => 'form-control date-field']) !!}
                    </div>

                    <!-- Error Container -->
                    <div class="form-group modal_error_wrapper">
                        <div class="alert alert-danger this_errors"></div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default modal-cancel-button" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary modal-form-submit-button"><span class="save-label">Save changes</span></button>
                </div>

                {!! Form::close() !!}

            </div>
        </div>
    </div>

    <!-- Delete modal -->
    @include('common.delete_modal')

    <!-- Success modal -->
    @include('common.success_modal')

    <h2>Authors</h2>
    <hr>

    <table id="authors_datatable" class="table table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>First Name</th>
                <th>Middle Name</th>
                <th>Last Name</th>
                <th>Description</th>
                <th>Date of Birth</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

@endsection

@section('footer-links')
    <script src="{{ asset('bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('js/form-process.js') }}"></script>
    <script>
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
                        });
                    }
                });

            });

        });
    </script>
@endsection

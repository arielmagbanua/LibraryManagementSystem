@extends('app')

@section('header-links')
    <link rel="stylesheet" href="{{ asset('bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
@endsection

@section('admin-members-class')
    active
@endsection

@section('admin-members-current')
    <span class="sr-only">(current)</span>
@endsection

@section('main-content')

    <div class="modal fade" id="member_modal_form" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">

                <?php
                    $formAttributes = array(
                            'url' 					=> url('user'),
                            'class'					=> 'modal_form',
                            'data-confirmation' 	=> '',
                            'data-process' 			=> 'add_member',
                            'data-parentmodal'      => '#member_modal_form',
                            'data-datatable'        => '#members_datatable',
                            'data-success'          => 'Member added successfully!',
                            'id'                    => 'member_form'

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
                            {!! Form::label('address', 'Address') !!}
                            {!! Form::textarea('address', '', ['class' => 'form-control', 'rows' => 2]) !!}
                        </div>

                        <div class="form-group">
                            {!! Form::label('email', 'Email') !!}
                            {!! Form::email('email', '', ['class' => 'form-control']) !!}
                        </div>

                        <div class="form-group">
                            {!! Form::label('birth_date', 'Date of Birth') !!}
                            {!! Form::text('birth_date', '', ['class' => 'form-control date-field']) !!}
                        </div>

                        <div class="form-group password-field">
                            {!! Form::label('password', 'Password') !!}
                            {!! Form::password('password', ['class' => 'form-control']) !!}
                        </div>

                        <div class="form-group password-field">
                            {!! Form::label('confirm_password', 'Confirm Password') !!}
                            {!! Form::password('confirm_password', ['class' => 'form-control']) !!}
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

    <h2>Members</h2>
    <hr>
    <table id="members_datatable" class="table table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>First Name</th>
                <th>Middle Name</th>
                <th>Last Name</th>
                <th>Address</th>
                <th>Email</th>
                <th>Date of Birth</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
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

            var membersListURL = baseURL+'/serverSide/membersList';

            $('#members_datatable').DataTable({
                'processing': true,
                'serverSide': true,
                'dom':'<"row" <"col-md-6" <"row" <"col-md-4" l><"col-md-8 add_member" >>><"col-md-6" f>>rt<"row" <"col-md-6" i><"col-md-6" p>>',
                'order': [[ 0, "asc" ]],
                'ajax': {
                    url: membersListURL,
                    type: 'get',
                    error: function() {}
                },
                'columns':[
                    {'data':'id'},
                    {'data':'first_name'},
                    {'data':'middle_name'},
                    {'data':'last_name'},
                    {'data':'address'},
                    {'data':'email'},
                    {'data':'birth_date'},
                    {'data':'actions','orderable': false}
                ]
            });

            $("div.add_member").html('<button class="btn btn-success" data-action="add_member" data-toggle="modal" data-target="#member_modal_form">Add Member</button>');

            $('#member_modal_form').on('show.bs.modal', function (e) {

                if(e.namespace === 'bs.modal')
                {

                    var invoker = $(e.relatedTarget);
                    var invokerAction = invoker.data('action');

                    var memberForm = $('#member_form');

                    if(invokerAction=='edit_member')
                    {
                        var memberID = invoker.data('id');
                        console.log(memberID);

                        //change the title of the modal
                        $('.modal-title').html('Edit Member');
                        //change the text of submit button
                        $('.save-label').html('Save Changes');

                        memberForm.attr('action',baseURL+'/user/'+memberID);
                        memberForm.attr('method','PATCH');
                        memberForm.data('success','Member updated successfully!');

                        //hide all password fields
                        $('.password-field').hide();

                        //row base ID
                        var rowBaseID = '#member-'+memberID+'-';

                        //get all the data from row
                        var firstName = $(rowBaseID+'first_name').html();
                        var middleName = $(rowBaseID+'middle_name').html();
                        var lastName = $(rowBaseID+'last_name').html();
                        var address = $(rowBaseID+'address').html();
                        var email = $(rowBaseID+'email').html();
                        var birthDate = $(rowBaseID+'birth_date').html();

                        //load the data in the form
                        var modal = $('#member_modal_form');
                        modal.find('#first_name').val(firstName);
                        modal.find('#middle_name').val(middleName);
                        modal.find('#last_name').val(lastName);
                        modal.find('#address').val(address);
                        modal.find('#email').val(email);
                        modal.find('#birth_date').val(birthDate);

                    }
                    else
                    {
                        $('.date-field').val('');
                        $('.password-field').show();

                        //change the title of the modal
                        $('.modal-title').html('Add Member');
                        //change the text of submit button
                        $('.save-label').html('Save');

                        memberForm.attr('action', baseURL+'/user');
                        memberForm.attr('method', 'POST');
                        memberForm.data('success','Member added successfully!');
                    }

                }

            });

            $('#delete_modal').on('show.bs.modal', function (e) {

                var invoker = $(e.relatedTarget);
                var invokerAction = invoker.data('action');

                if(invokerAction=='delete_member')
                {
                    var memberID = invoker.data('id');
                    console.log(memberID);
                    //load the id to the hidden field of the modal for deletion
                    $('#id_to_delete').val(memberID);

                    //set the yes button to defaults
                    var deleteButton = $('#delete_button');
                    var deleteLabel = deleteButton.find('span.delete-label');
                    deleteLabel.html('Yes Delete');
                    deleteLabel.removeClass('fa fa-spin fa-spinner');
                    deleteButton.removeAttr('disabled');
                    deleteButton.show();
                    $('#delete_cancel_button').html('Cancel');

                    //set the body message to default
                    $('#delete_modal_message').html('Are you sure you want to delete this member?');
                }

            });

            $('#delete_button').click(function()
            {
                //get the member id
                var memberID = $('#id_to_delete').val();
                console.log(memberID);

                var deleteButton = $(this);
                var deleteCancelButton = $('#delete_cancel_button');

                //modify the delete label to show spinner load
                var deleteLabel = deleteButton.find('span.delete-label');
                deleteLabel.html('');
                deleteLabel.addClass('fa fa-spin fa-spinner');
                deleteButton.prop('disabled',true);
                deleteCancelButton.prop('disabled',true);

                var deleteURL = baseURL + '/user/'+memberID;
                console.log(deleteURL);

                $.ajax({
                    type: 'DELETE',
                    url: deleteURL,
                    success: function()
                    {
                        //remove the record in datatable
                        var table = $('#members_datatable').DataTable();
                        //var row = $('#member-'+memberID+'-first_name').parents('tr');
                        //table.row(row).remove().draw();

                        table.ajax.reload(function(){
                            //Change the message of modal and hide the delete button
                            $('#delete_modal_message').html('The member was successfully deleted!');
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

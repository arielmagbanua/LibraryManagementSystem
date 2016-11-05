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

                {!! Form::open(['url' => url('member/create'), 'id' => 'member_form']) !!}

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
                            {!! Form::text('birth_date', '', ['class' => 'form-control']) !!}
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" id="modal_form_submit" class="btn btn-primary modal_submit">Save changes</button>
                    </div>

                {!! Form::close() !!}

            </div>
        </div>
    </div>

    <!-- Delete modal -->
    @include('common.delete_modal')

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

                var invoker = $(e.relatedTarget);
                var invokerAction = invoker.data('action');

                if(invokerAction=='edit_member')
                {
                    var memberID = invoker.data('id');
                    console.log(memberID);

                    //change the title of the modal
                    $('.modal-title').html('Edit Member');
                    //change the text of submit button
                    $('#modal_form_submit').html('Save Changes');
                }
                else
                {
                    //change the title of the modal
                    $('.modal-title').html('Add Member');
                    //change the text of submit button
                    $('#modal_form_submit').html('Save');

                    $('#member_form').trigger("reset");
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
                        var row = $('#member-'+memberID+'-first_name').parents('tr');
                        table.row(row).remove().draw();

                        //Change the message of modal and hide the delete button
                        $('#delete_modal_message').html('The member was successfully deleted!');
                        deleteButton.hide();
                        deleteCancelButton.html('Close');
                        deleteCancelButton.removeAttr('disabled');
                    }
                });

            });

        });
    </script>
@endsection

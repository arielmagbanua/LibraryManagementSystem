@extends('app')

@section('header-links')
    <link rel="stylesheet" href="{{ asset('bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@endsection

@section('admin-members-class')
    active
@endsection

@section('admin-members-current')
    <span class="sr-only">(current)</span>
@endsection

@section('main-content')

    <div class="modal fade" id="book_modal_form" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="book_modal_form_label">Modal title</h4>
                </div>
                <div class="modal-body">
                    Book modal form
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <h2>Members</h2>
    <hr>
    <table id="members_datatable" class="table">
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
    <script>
        $(document).ready(function()
        {
            var baseURL = $('#baseURL').html();
            var membersListURL = baseURL+'/serverSide/membersList';

            $('#members_datatable').DataTable({
                'processing': true,
                'serverSide': true,
                'order': [[ 0, "desc" ]],
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
        });
    </script>
@endsection

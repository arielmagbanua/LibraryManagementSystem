@extends('app')

@section('header-links')
    <link rel="stylesheet" href="{{ asset('bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@endsection

@section('admin-authors-class')
    active
@endsection

@section('admin-authors-current')
    <span class="sr-only">(current)</span>
@endsection

@section('main-content')

    <h2>Authors</h2>
    <hr>

    <table id="authors_datatable" class="table">
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
    <script>
        $(document).ready(function(){
            var baseURL = $('#baseURL').html();
            var authorsListURL = baseURL+'/serverSide/authorsList';

            $('#authors_datatable').DataTable({
                'processing': true,
                'serverSide': true,
                'order': [[ 0, "desc" ]],
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
        });
    </script>
@endsection

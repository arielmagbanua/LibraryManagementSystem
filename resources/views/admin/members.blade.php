@extends('master')

@section('header-links')
@endsection

@section('admin-members-class')
    active
@endsection

@section('main-content')

    <div class="row">

        <div class="col s12"><h4>Members</h4></div>

        <div class="col s12">
            <table id="members_datatable">
                <thead>
                <tr>
                    <th data-field="id">ID</th>
                    <th data-field="first_name">First Name</th>
                    <th data-field="middle_name">Middle Name</th>
                    <th data-field="last_name">Last Name</th>
                    <th data-field="address">Address</th>
                    <th data-field="email">Email</th>
                    <th data-field="birth_date">Date of Birth</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

    </div>
@endsection

@section('footer-links')
    <script src="{{ asset('bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script>
        $(document).ready(function()
        {
            $('#members_datatable').DataTable();
        });
    </script>
@endsection

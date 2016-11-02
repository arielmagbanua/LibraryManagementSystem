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

    <h2>Members</h2>
    <hr>
    <table id="members_datatable" class="table">
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
        <tbody>
            @foreach($members as $member)
                <tr>
                    <td>{{ $member->id }}</td>
                    <td>{{ $member->first_name }}</td>
                    <td>{{ $member->middle_name }}</td>
                    <td>{{ $member->last_name }}</td>
                    <td>{{ $member->address }}</td>
                    <td>{{ $member->email }}</td>
                    <td>{{ $member->birth_date }}</td>
                    <th>Actions</th>
                </tr>
            @endforeach
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
                    error: function()
                    {

                    }
                }
            });
        });
    </script>
@endsection

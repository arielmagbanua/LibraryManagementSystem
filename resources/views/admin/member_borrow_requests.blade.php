@extends('app')

@section('header-links')
    <link rel="stylesheet" href="{{ asset('bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@endsection

@section('admin-borrowRequests-class')
    active
@endsection

@section('admin-borrowRequests-current')
    <span class="sr-only">(current)</span>
@endsection

@section('main-content')

    <div class="modal fade" id="request_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Cancel Borrow Request</h4>
                </div>

                <div class="modal-body">
                    {!! Form::hidden('request_id_to_cancel_approve', '', ['id' => 'request_id_to_cancel_approve', 'class' => 'form-control']) !!}
                    <p id="request_modal_message">Are you sure you want to cancel this book borrow request?</p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" id="cancel_button" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-default" id="yes_button"><span class="yes-label">Yes</span></button>
                </div>

            </div>
        </div>
    </div>

    <h2>Borrow Requests</h2>
    <br>
    <p>These are the book borrow requests made by members and are waiting for your approval.</p>
    <hr>

    <table id="pending_borrowed_books_datatable" class="table table-hover">
        <thead>
        <tr>
            <th>Title</th>
            <th>ISBN</th>
            <th>Overdue Fine</th>
            <th>Member</th>
            <th>Email</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody></tbody>
    </table>

@endsection

@section('footer-links')
    <script src="{{ asset('bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/admin/member-borrow-requests.min.js') }}"></script>
@endsection
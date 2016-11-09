@extends('app')

@section('header-links')
    <link rel="stylesheet" href="{{ asset('bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@endsection

@section('member-pending-borrow-class')
    active
@endsection

@section('member-pending-borrow-current')
    <span class="sr-only">(current)</span>
@endsection

@section('main-content')

    <div class="modal fade" id="cancel_request_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Cancel Borrow Request</h4>
                </div>

                <div class="modal-body">
                    {!! Form::hidden('request_id_to_cancel', '', ['id' => 'request_id_to_cancel', 'class' => 'form-control']) !!}
                    <p id="cancel_request_modal_message">Are you sure you want to cancel this book borrow request?</p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" id="cancel_borrow_request_button" data-dismiss="modal">Cancel</button>
                    <button type="button" id="yes_cancel_borrow_request_button" class="btn btn-danger"><span class="yes-label">Yes</span></button>
                </div>

            </div>
        </div>
    </div>

    <h2>Pending Borrow Requests</h2>
    <br>
    <p>These are your pending borrow request and they are not yet approved by a librarian.</p>
    <hr>

    <table id="pending_borrowed_books_datatable" class="table table-hover">
        <thead>
            <tr>
                <th>Title</th>
                <th>ISBN</th>
                <th>Overdue Fine</th>
                <th>Shelf Location</th>
                <th>Borrow Start Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

@endsection

@section('footer-links')
    <script src="{{ asset('bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/member/pending-books.min.js') }}"></script>
@endsection
@extends('app')

@section('header-links')
    <link rel="stylesheet" href="{{ asset('bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@endsection

@section('admin-borrowed-class')
    active
@endsection

@section('admin-borrowed-current')
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
                    {!! Form::hidden('request_id_to_return', '', ['id' => 'request_id_to_return', 'class' => 'form-control']) !!}
                    <p id="request_modal_message">Are you sure you want to cancel this book borrow request?</p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" id="cancel_button" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-default" id="yes_button"><span class="yes-label">Yes</span></button>
                </div>

            </div>
        </div>
    </div>

    <h2>Borrowed Books</h2>
    <br>
    <p>These are the borrowed books by the members. Here you can mark the books as returned if the member has indeed returned the books.</p>
    <hr>

    <table id="pending_borrowed_books_datatable" class="table table-hover">
        <thead>
        <tr>
            <th>Title</th>
            <th>Author</th>
            <th>ISBN</th>
            <th>Borrow Start Date</th>
            <th>Fine</th>
            <th>Borrower</th>
            <th>Borrower Email</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody></tbody>
    </table>

@endsection

@section('footer-links')
    <script src="{{ asset('bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/admin/member-borrowed-books.min.js') }}"></script>
@endsection
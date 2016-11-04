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

                {!! Form::open(['url' => url('author/create'), 'id' => 'author_form']) !!}

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
            var authorsListURL = baseURL+'/serverSide/authorsList';

            $('#authors_datatable').DataTable({
                'processing': true,
                'serverSide': true,
                'dom':'<"row" <"col-md-6" <"row" <"col-md-4" l><"col-md-8 add_author" >>><"col-md-6" f>>rt<"row" <"col-md-6" i><"col-md-6" p>>',
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

            $("div.add_author").html('<button class="btn btn-success" data-action="add_author" data-toggle="modal" data-target="#author_modal_form">Add Author</button>');

            $('#author_modal_form').on('show.bs.modal', function (e) {

                var invoker = $(e.relatedTarget);
                var invokerAction = invoker.data('action');

                if(invokerAction=='edit_author')
                {
                    var authorID = invoker.data('id');
                    console.log(authorID);

                    //change the title of the modal
                    $('.modal-title').html('Edit Author');
                    //change the text of submit button
                    $('#modal_form_submit').html('Save Changes');
                }
                else
                {
                    //change the title of the modal
                    $('.modal-title').html('Add Author');
                    //change the text of submit button
                    $('#modal_form_submit').html('Save');

                    $('#author_form').trigger("reset");
                }

            });
        });
    </script>
@endsection

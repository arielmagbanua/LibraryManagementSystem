@extends('master')

@section('header-links')
@endsection

@section('admin-books-class')
    active
@endsection

@section('main-content')

    <div class="row">

        <div class="col s12"><h4>Books</h4></div>

        <div class="col s12">
            <table>
                <thead>
                    <tr>
                        <th data-field="title">Title</th>
                        <th data-field="author_id">Author</th>
                        <th data-field="isbn">ISBN</th>
                        <th data-field="quantity">Quantity</th>
                        <th data-field="overdue_fine">Overdue Fine</th>
                        <th data-field="shelf_location">Shelf Location</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

    </div>
@endsection

@section('footer-links')
@endsection

var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function(mix) {
    mix.sass('app.scss');

    //javascript minifying for admin pages
    mix.scripts('admin/authors.js', 'public/js/admin/authors.min.js')
        .scripts('admin/books.js', 'public/js/admin/books.min.js')
        .scripts('admin/index.js', 'public/js/admin/index.min.js')
        .scripts('admin/member-borrow-requests.js', 'public/js/admin/member-borrow-requests.min.js')
        .scripts('admin/member-borrowed-books.js', 'public/js/admin/member-borrowed-books.min.js')
        .scripts('admin/members.js', 'public/js/admin/members.min.js');

});


<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Login">

    <!-- Materialize CSS -->
    <link rel="stylesheet" href="{{ asset('bower_components/materialize/dist/css/materialize.min.css') }}">

    @yield('header-links')
</head>

<body>

    <div class="container">
        @yield('main-content')
    </div>

    <script src="{{ asset('bower_components/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('bower_components/materialize/dist/js/materialize.min.js') }}"></script>

    @yield('footer-links')
</body>

</html>
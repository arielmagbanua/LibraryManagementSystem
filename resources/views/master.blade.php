<!DOCTYPE html>
<html>

<head>
    <title>The Library</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Library Management System">
    <meta name="_token" content="{{ csrf_token() }}" />

    <!-- CSS for materializecss -->
    <link rel="stylesheet" href="{{ asset('bower_components/materialize/dist/css/materialize.min.css') }}">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    @yield('header-links')
</head>

<body>

<!-- Dropdown Structure -->
    <ul id="nav_three_dot_menu" class="dropdown-content">
        <li><a href="#">About</a></li>
        <li><a href="{{ url('auth/logout') }}">Logout</a></li>
    </ul>

    <nav>
        <div class="nav-wrapper red darken-1">
            <a href="{{url()}}" class="brand-logo">The Library</a>
            <a href="#" data-activates="mobile-menu" class="button-collapse"><i class="material-icons">menu</i></a>
            <ul id="nav-mobile" class="right hide-on-med-and-down">

                @if(auth()->check() && auth()->user()->account_type == 1)
                    <li class="@yield('admin-reports-class')"><a href="{{ url('admin/reports') }}">Reports</a></li>
                    <li class="@yield('admin-books-class')"><a href="{{ url('admin/books') }}">Books</a></li>
                    <li class="@yield('admin-members-class')"><a href="{{ url('admin/members') }}">Members</a></li>
                @endif

                @if(auth()->check() && auth()->user()->account_type == 2)
                    <li class="@yield('member-home-class')"><a href="{{ url('member/home') }}">Home</a></li>
                    <li class="@yield('member-books-class')"><a href="{{ url('member/books') }}">Books</a></li>
                    <li class="@yield('member-borrowed-class')"><a href="{{ url('member/borrowed') }}">Borrowed Books</a></li>
                @endif

                <!-- Dropdown Trigger -->
                <li><a class="dropdown-button" href="#!" data-activates="nav_three_dot_menu"><i class="material-icons right">more_vert</i></a></li>

            </ul>
            <ul class="side-nav" id="mobile-menu">

                @if(auth()->check() && auth()->user()->account_type == 1)
                    <li class="@yield('admin-reports-class')"><a href="{{ url('admin/reports') }}">Reports</a></li>
                    <li class="@yield('admin-books-class')"><a href="{{ url('admin/books') }}">Books</a></li>
                    <li class="@yield('admin-members-class')"><a href="{{ url('admin/members') }}">Members</a></li>
                @endif

                @if(auth()->check() && auth()->user()->account_type == 2)
                    <li class="@yield('member-home-class')"><a href="{{ url('member/home') }}">Home</a></li>
                    <li class="@yield('member-books-class')"><a href="{{ url('member/books') }}">Books</a></li>
                    <li class="@yield('member-borrowed-class')"><a href="{{ url('member/borrowed') }}">Borrowed Books</a></li>
                @endif

                <li><a href="#">About</a></li>
                <li><a href="{{ url('auth/logout') }}">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div id="container">
        @yield('main-content')
    </div>

    <script src="{{ asset('bower_components/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('bower_components/materialize/dist/js/materialize.min.js') }}"></script>
    @yield('footer-links')

    <script>
        $(document).ready(function(){
            $(".dropdown-button").dropdown();
            $(".button-collapse").sideNav();
        });
    </script>
</body>

</html>
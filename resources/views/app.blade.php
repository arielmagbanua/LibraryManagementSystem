<!DOCTYPE html>
<html>

<head>
    <title>The Library</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Library Management System">
    <meta name="_token" content="{{ csrf_token() }}" />

    <!-- Bootstrap css -->
    <link rel="stylesheet" href="{{ asset('bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('bower_components/bootstrap/dist/css/bootstrap-theme.min.css') }}">

    @yield('header-links')
</head>

<body>

    <nav class="navbar navbar-default">
        <div class="container-fluid">

            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="{{ url() }}">The Library</a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">

                <ul class="nav navbar-nav">
                    <!--
                    <li class="active"><a href="#">Link <span class="sr-only">(current)</span></a></li>
                    <li><a href="#">Link</a></li>
                    -->
                    @if(auth()->check() && auth()->user()->account_type == 1)
                        <li class="@yield('admin-reports-class')"><a href="{{ url('admin/reports') }}">Reports @yield('admin-reports-current')</a></li>
                        <li class="@yield('admin-authors-class')"><a href="{{ url('admin/authors') }}">Authors @yield('admin-authors-current')</a></li>
                        <li class="@yield('admin-books-class')"><a href="{{ url('admin/books') }}">Books @yield('admin-books-current')</a></li>
                        <li class="@yield('admin-members-class')"><a href="{{ url('admin/members') }}">Members @yield('admin-members-current')</a></li>
                        <li class="@yield('admin-borrowRequests-class')"><a href="{{ url('admin/borrowRequests') }}">Members @yield('admin-borrowRequests-current')</a></li>
                    @endif

                    @if(auth()->check() && auth()->user()->account_type == 2)
                        <li class="@yield('member-home-class')"><a href="{{ url('member/home') }}">Home @yield('member-home-current')</a></li>
                        <li class="@yield('member-books-class')"><a href="{{ url('member/books') }}">Books @yield('member-home-current')</a></li>
                        <li class="@yield('member-borrowed-class')"><a href="{{ url('member/borrowed') }}">Borrowed Books @yield('member-home-current')</a></li>
                    @endif

                </ul>

                <ul class="nav navbar-nav navbar-right">
                    @if(auth()->check())
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">{{ auth()->user()->first_name }} <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="#">About</a></li>
                                <li role="separator" class="divider"></li>
                                <li><a href="{{ url('auth/logout') }}">Logout</a></li>
                            </ul>
                        </li>
                    @endif
                </ul>

            </div><!-- /.navbar-collapse -->

        </div>
    </nav>

    <div id="main_content" class="container-fluid">
        @yield('main-content')
    </div>

    <span hidden id="baseURL">{{ url('/') }}</span>

<script src="{{ asset('bower_components/jquery/dist/jquery.min.js') }}"></script>
<script src="{{ asset('bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
@yield('footer-links')

</body>

</html>
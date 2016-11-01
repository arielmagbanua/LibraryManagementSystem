@extends('auth.template')

@section('header-links')
    <style>
        /* label color */
        .input-field label {
            color: #FFFFFF;
        }
        /* label focus color */
        .input-field input[type=text]:focus + label {
            color: #FFFFFF;
        }
        /* label underline focus color */
        .input-field input[type=text]:focus {
            border-bottom: 1px solid #FFFFFF;
            box-shadow: 0 1px 0 0 #ffffff;
        }
        /* valid color */
        .input-field input[type=text].valid {
            border-bottom: 1px solid #ffffff;
            box-shadow: 0 1px 0 0 #FFFFFF;
        }
        /* invalid color */
        .input-field input[type=text].invalid {
            border-bottom: 1px solid #ff8a80;
            box-shadow: 0 1px 0 0 #ff8a80;
        }
        /* icon prefix focus color */
        .input-field .prefix.active {
            color: #ffffff;
        }

        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus,
        input:-webkit-autofill:active {
            -webkit-transition: "color 9999s ease-out, background-color 9999s ease-out";
            -webkit-transition-delay: 9999s;
        }
    </style>
@endsection

@section('main-content')
    <div class="row">
        <div class="col l6 offset-l3 col s12 m12">
            <div class="card red darken-1">

                @if (count($errors) > 0)
                    <div class="card-content white-text">
                        <div class="alert alert-danger">
                            <strong>Whoops!</strong> There were some problems with your input.<br><br>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <form role="form" method="POST" action="{{ url('/password/reset') }}">
                    <div class="card-content white-text">
                        <span class="card-title">The Library Login</span>
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="row">
                            <div class="input-field col s12">
                                <input id="email" type="email" class="validate" name="email" value="{{ old('email') }}">
                                <label for="email">Email</label>
                            </div>
                            <div class="input-field col s12">
                                <input id="password" type="password" class="validate" name="password">
                                <label for="password">Password</label>
                            </div>
                            <div class="input-field col s12">
                                <input id="password_confirmation" type="password" class="validate" name="password_confirmation">
                                <label for="password_confirmation">Confirm Password</label>
                            </div>
                        </div>
                    </div>

                    <div class="card-action">
                        <button type="submit" class="waves-effect waves-light btn red lighten-2">Reset Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
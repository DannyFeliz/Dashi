 <!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Dashi') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/pages.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
</head>
<body class="template-{{ collect(\Request::segments())->implode('-') }}">
    <div id="app">
        <nav class="header md-header dark" data-pages="header" data-pages-header="autoresize">
           <div class="container relative">
              <div class="pull-left">
                 <div class="header-inner inline-logo">
                    <a class="d-flex" href="{{ url('/') }}"><img src="/img/dashi-inline-logo.png" width="150" height="34" data-src-retina="assets/images/logo_white_2x.png" alt=""></a>
                 </div>
              </div>
              <div class="pull-right menu-content clearfix" data-pages="menu-content" data-pages-direction="slideRight" id="header">
                 <div class="header-inner">
                     <ul class="menu">
                        @if (Auth::guest())
                            <li><a href="{{ route('login') }}">Login</a></li>
                            <li><a href="{{ route('register') }}">Register</a></li>
                        @else
                            <li class="btn-group dropdown dropdown-default">
                                <button class="btn btn-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="z-index:3;background-color: transparent;border: 0;color: white;">
                                    {{ Auth::user()->name }}
                                </button>
                                <div class="dropdown-menu" style="width: 100%; z-index:1 !important;">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                        onclick="event.preventDefault();
                                                 document.getElementById('logout-form').submit();">
                                        Logout
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        {{ csrf_field() }}
                                    </form>
                                </div>
                            </li>
                        @endif
                    </ul>
                 </div>
              </div>
           </div>
        </nav>

        @yield('content')

    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/jquery-3.2.1.slim.min.js') }}"></script>
    <script src="{{ asset('js/tether.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/scripts.js') }}"></script>
</body>
</html>

<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="userId" content="{{ auth()->check() ? auth()->id() : ''}}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <meta name="description" content="">

    <!-- Favicons -->



    <!-- Scripts -->

    <!-- Fonts -->
    <link rel="shortcut icon" href="{{ url('/') }}/public/favicon.ico" type="image/x-icon">
    <link rel="icon" href="{{ url('/') }}/public/favicon.ico" type="image/x-icon">
    <link rel="dns-prefetch" href="//fonts.gstatic.com">

    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <!-- Google font (font-family: 'Roboto', sans-serif; Poppins ; Satisfy) -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,300i,400,400i,500,600,600i,700,700i,800" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ url('/') }}/public/css/app.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ url('/') }}/public/frontend/css/plugins.css">
    <link rel="stylesheet" href="{{ url('/') }}/public/frontend/css/style.css">

    <!-- Modernizer js -->
    <script src="{{ url('/') }}/public/frontend/js/vendor/modernizr-3.5.0.min.js"></script>

    @yield('style')
</head>
<body>
    <div id="app">

        <div class="wrapper" id="wrapper">
            @include('frontend.includes.header')

        <main>
            @include('frontend.includes.alerts.success')
            @include('frontend.includes.alerts.errors')
            @yield('content')
            <example-component></example-component>
        </main>
            @include('frontend.includes.footer')
        </div>
    </div>
    <script src="{{url('/')}}/public/js/app.js"></script>
    <script src="{{url('/')}}/public/frontend/js/vendor/jquery-3.2.1.min.js"></script>
    <script src="{{url('/')}}/public/frontend/js/popper.min.js"></script>
    <script src="{{url('/')}}/public/frontend/js/bootstrap.min.js"></script>
    <script src="{{url('/')}}/public/frontend/js/plugins.js"></script>
    <script src="{{url('/')}}/public/frontend/js/active.js"></script>

    <script src="{{url('/')}}/public/frontend/js/bootstrap-fileinput/js/plugins/piexif.min.js"></script>
    <script src="{{url('/')}}/public/frontend/js/bootstrap-fileinput/js/plugins/sortable.min.js"></script>
    <script src="{{url('/')}}/public/frontend/js/bootstrap-fileinput/js/fileinput.min.js"></script>
    <script src="{{url('/')}}/public/frontend/js/bootstrap-fileinput/themes/fa/theme.min.js"></script>





    <script src="{{url('/')}}/public/frontend/js/custom.js"></script>

@yield('script')

</body>
</html>

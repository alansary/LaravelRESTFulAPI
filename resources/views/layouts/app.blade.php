<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8"/>
        <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
        <meta name="viewport" content="width=device-width, initial-scale=1"/>
        <meta name="description" content="RESTFul API"/>
        <meta name="author" content="Mohamed Alansary"/>
        <link rel="icon" href="{{asset('restfulapi.png')}}"/>

        <title>{{config('app.name', 'RESTFulAPI')}}</title>

        <!-- Styles -->
        <link rel="stylesheet" href="{{asset('css/bootstrap.min.css')}}"/>
        <link rel="stylesheet" href="{{asset('css/ie10-viewport-bug-workaround.css')}}"/>
        <link rel="stylesheet" href="{{asset('css/blog.css')}}"/>
        <link rel="stylesheet" href="{{asset('css/prism.css')}}"/>
    </head>
    <body>
        @include('inc.masthead')
        <div class="container">
            @include('inc.messages')
            @yield('content')
        </div>
        @include('inc.footer')
        <!-- Scripts -->
        <script src="{{asset('js/jquery.min.js')}}"></script>
        <script>window.jQuery || document.write('<script src="{{asset('js/jquery.min.js')}}"><\/script>')</script>
        <script src="{{asset('js/bootstrap.min.js')}}"></script>
        <script src="{{asset('js/ie10-viewport-bug-workaround.js')}}"></script>
        <script src="{{asset('js/prism.js')}}"></script>
    </body>
</html>

<!DOCTYPE html>
<html class="h-full w-full"lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
    <meta charset="UTF-8" />
   <meta name="viewport" content="width=device-width, initial-scale=1.0" />
   
   <link href="{{ asset('css/app.css') }}" rel="stylesheet">
   <script src="{{ asset('js/app.js') }}"></script>

    <title>Eglise-Saint-Remi</title>

       
        
    </head>

    <body class="h-full w-full">
        @yield('content')
    </div>

</html>

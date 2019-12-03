<!doctype html>
<html lang="en" class="h-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="robots" content="noindex, nofollow">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }}</title>

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css">
</head>
<body class="bg-gray-600 scrollbar-lg">
    <div id="app"></div>

    <script>
        window.data = {
            'config': @json($config),
            'endpoint': '{{ $endpoint }}',
            'shareEndpoint': '{{ $shareEndpoint }}'
        }
    </script>

    <script>{!! $navigate_src !!}</script>
    <script>{!! $laracatch_src !!}</script>
    <script>
        window.Navigator = window.navigate(window.data);

        Navigator.start();
    </script>
</body>
</html>
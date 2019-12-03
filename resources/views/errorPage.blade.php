<!doctype html>
<html lang="en" class="theme-light">
<head>
    <!-- Hide dumps asap -->
    <style>
        pre.sf-dump {
            display: none !important;
        }
    </style>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="robots" content="noindex, nofollow">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }}</title>

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css">
</head>
<body class="scrollbar-lg bg-gray-100">
    <div id="app"></div>

    <script>{!! $laracatch_src !!}</script>
    <script>
        window.data = {
            'error': {!! $errorModel->toJson() !!},
            'config': @json($config),
            'telescopeUrl': '{{ $telescopeUrl }}',
            'shareEndpoint': '{{ $shareEndpoint }}',
            'defaultTab': '{{ $defaultTab }}',
            'defaultProps': @json($defaultProps)
        }
    </script>

    <script>
        window.Laracatch = window.laracatcher(window.data);

        Laracatch.start('#app');
    </script>

</body>
</html>

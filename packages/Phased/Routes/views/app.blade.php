<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vuex

    @foreach (config('phase.assets.sass') as $styles)
    <link rel="stylesheet" type="text/css" href="{{ mix(str_replace('sass', 'css', str_replace('scss', 'css', $styles))) }}">
    @endforeach
</head>
<body>
    @app
    {{-- Load all required scripts --}}
    @foreach (config('phase.assets.js') as $script)
    <script src="{{ mix($script) }}"></script>
    @endforeach
</body>
</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @foreach (config('phase.assets.sass') as $styles)
    <link rel="stylesheet" type="text/css" href="{{ mix(str_replace('sass', 'css', str_replace('scss', 'css', $styles))) }}">
    @endforeach


</head>
 <body>
    {!! ssr('js/app-server.js')
            ->context('phased', Vuex::toArray())
            // If ssr fails, we need a container to render the app client-side
            ->fallback('<div id="app"></div>')
            ->render() !!}
    {{-- @foreach (config('phase.assets.js') as $script)
    <script src="{{ mix($script) }}"></script>
    @endforeach --}}
    @if(config('phase.state'))
    @vuex
    @endif
    <script defer src="{{ mix('js/app-client.js') }}"></script>
</body>
</html>

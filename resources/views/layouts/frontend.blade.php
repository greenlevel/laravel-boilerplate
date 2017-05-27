<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    @include('frontend.scripts.gtm')

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title or config('app.name') }}</title>

    @if (!empty($description))
    <meta name="description" content="{{ $description }}">
    @endif
    @yield('metas')

    <!-- Styles -->
    @if (!$hmr)
    <link rel="stylesheet" href="{{ assets('css/frontend.css') }}">
    @endif
    @yield('styles')
</head>
<body id="@yield('body_id')" class="@yield('body_class')">
    @include('frontend.scripts.gtmiframe')

    <div id="app">
        @include('frontend.partials.header')
        @include('frontend.partials.nav')

        <div class="container">
            @include('partials.messages')
            @yield('content')
        </div>
    </div>

    @include('frontend.partials.footer')

    <!-- Scripts -->
    <script src="{{ assets('js/manifest.js') }}"></script>
    <script src="{{ assets('js/vendor.js') }}"></script>
    <script src="{{ assets('js/frontend.js') }}"></script>

    @if (config('app.locale') !== 'en')
        <script src="{{ assets('i18n/moment.' . config('app.locale') . '.js') }}"></script>
        <script src="{{ assets('i18n/select2.' . config('app.locale') . '.js') }}"></script>
    @endif

    @yield('scripts')
</body>
</html>

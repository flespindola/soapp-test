<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Soapp') }}</title>

        <!-- Fonts -->
        <!--<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">-->

        <!-- Scripts -->
        @routes
        @inertiaHead

        <!-- Styles -->
        <link rel="stylesheet" href="{{ mix('css/app.css') }}">

        <link id="theme-css" rel="stylesheet" type="text/css" href="/theme/theme-light/green/theme.css">
        <link id="layout-css" rel="stylesheet" type="text/css" href="/layout/css/layout-light.css">

    </head>
    <body class="antialiased">
        <noscript>
            <strong>
                We're sorry but Soapp doesn't work properly without JavaScript enabled. Please enable it to continue.
            </strong>
        </noscript>
        <div id="app_loader">
            <div class="splash-screen">
                <div class="splash-loader-container">
                    <svg class="splash-loader" width="65px" height="65px" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg">
                        <circle class="splash-path" fill="none" stroke-width="6" stroke-linecap="round" cx="33" cy="33" r="30"></circle>
                    </svg>
                </div>
            </div>
        </div>

        @inertia

        <script src="{{ mix('js/manifest.js') }}" defer></script>
        <script src="{{ mix('js/vendor.js') }}" defer></script>
        <script src="{{ mix('js/app.js') }}" defer></script>
    </body>
</html>

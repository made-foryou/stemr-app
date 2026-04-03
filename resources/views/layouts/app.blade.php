<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen antialiased" style="background-color: var(--color-vota-bg); color: var(--color-vota-text);">
        {{ $slot }}

        @fluxScripts
    </body>
</html>

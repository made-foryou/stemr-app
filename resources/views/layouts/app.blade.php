<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen antialiased" style="background-color: var(--color-vota-bg); color: var(--color-vota-text);">
        {{-- Subtle background gradient for depth --}}
        <div class="pointer-events-none fixed inset-0 -z-10" style="background: radial-gradient(ellipse at 20% 0%, rgba(107, 53, 104, 0.03) 0%, transparent 60%), radial-gradient(ellipse at 80% 100%, rgba(196, 112, 63, 0.02) 0%, transparent 50%);"></div>
        {{ $slot }}

        @fluxScripts
    </body>
</html>

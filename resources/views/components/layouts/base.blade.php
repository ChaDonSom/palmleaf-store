<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Laravel'))</title>

    <meta name="description" content="Clothing that carries good news">

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">
    @stack('fonts')

    <!-- Icon -->
    <link rel="icon" href="{{ asset('logo.png') }}">

    <!-- Styles -->
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')

    <!-- Scripts -->
    @stack('scripts')

    @stack('head')
</head>

<body @class(['antialiased text-gray-900' => !isset($bodyClasses), $bodyClasses ?? ''])>
    @yield('navigation')

    <main>
        {{ $slot }}
    </main>

    @yield('footer')

    @stack('modals')

    <x-toast-notification />

    @livewireScripts
</body>

</html>

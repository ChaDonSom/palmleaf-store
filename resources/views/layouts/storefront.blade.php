<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- Styles -->
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <meta name="description" content="Hand-made memorabilia by Maria Somero">

    <!-- Scripts -->
    {{-- <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script> --}}
    <link rel="icon" href="{{ asset('favicon.svg') }}">
</head>

<body class="antialiased text-gray-900">
    @livewire('components.navigation')

    <main>
        {{ $slot }}
    </main>

    <x-footer />

    @stack('modals') <!-- TODO: new demo doesn't have this -->

    @livewireScripts
</body>

</html>

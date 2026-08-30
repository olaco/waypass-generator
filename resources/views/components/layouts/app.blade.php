<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            <!-- Top Navigation -->
            @include('layouts.navigation')

            <!-- Page Heading (moved inside the main content area) -->
            @if (isset($header))
                <header class="bg-white shadow fixed w-full z-20 top-16">
                    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                        <h1 class="text-xl font-bold text-gray-900">{{ $header }}</h1>
                    </div>
                </header>
            @endif

            <!-- Main Layout with Sidebar -->
            <div class="flex pt-16">
                <!-- Left Sidebar -->
                <x-layouts.sidebar />

                <!-- Page Content -->
                <main class="flex-1 ml-64 p-6 {{ isset($header) ? 'pt-20' : '' }}">
                    <div class="max-w-7xl mx-auto">
                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>

        @livewireScripts
    </body>
</html>

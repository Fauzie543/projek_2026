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

    @vite(['resources/css/app.css'])
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.tailwindcss.min.css">
    
</head>
<body class="font-sans antialiased bg-gray-50">

    {{-- Navbar + Sidebar --}}
    @include('layouts.navbar')

    <!-- Wrapper Konten -->
    <div class="p-4 sm:ml-64">
        <div class="p-4 bg-white rounded-lg shadow-sm mt-12">
            {{-- Section Header opsional --}}
            @hasSection('header')
                <div class="mb-4">
                    <h1 class="text-xl font-semibold text-gray-800 dark:text-gray-100">
                        @yield('header')
                    </h1>
                </div>
            @endif

            {{-- Section Content --}}
            @yield('content')
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
    
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.tailwindcss.min.js"></script>
    
    @stack('scripts')
</body>
</html>

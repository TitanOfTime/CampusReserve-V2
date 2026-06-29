<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'CampusReserve') }}</title>

        <!-- Inline SVG Favicon -->
        <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%232563eb'%3E%3Crect width='24' height='24' rx='6' fill='%232563eb'/%3E%3Ctext x='12' y='16' font-size='11' font-weight='bold' font-family='sans-serif' fill='white' text-anchor='middle'%3ECR%3C/text%3E%3C/svg%3E">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles
        <style>
            body { font-family: 'Inter', sans-serif; }
        </style>
        <script>
            document.documentElement.setAttribute('data-theme', localStorage.getItem('campus-theme') || 'aurora');
        </script>
    </head>
    <body class="theme-shell min-h-screen">
        <div class="font-sans text-[color:var(--text)] antialiased">
            {{ $slot }}
        </div>

        @livewireScripts
    </body>
</html>

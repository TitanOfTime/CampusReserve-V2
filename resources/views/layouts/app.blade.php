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
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles

        <style>
            body { font-family: 'Inter', sans-serif; }
        </style>
    </head>
    <body class="font-sans antialiased bg-slate-100">
        <x-banner />

        <div class="min-h-screen flex" x-data="{ mobileOpen: false }">

            <!-- ===================== MOBILE OVERLAY ===================== -->
            <div x-show="mobileOpen"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="mobileOpen = false"
                 style="display:none;"
                 class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm md:hidden">
            </div>

            <!-- ===================== SIDEBAR ===================== -->
            <aside class="fixed top-0 left-0 h-full z-50 flex flex-col bg-slate-900 transition-transform duration-300 ease-in-out
                          w-64
                          -translate-x-full md:translate-x-0"
                   :class="mobileOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'">

                <!-- Logo -->
                <div class="flex items-center gap-3 px-5 py-6 border-b border-slate-800">
                    <div class="w-9 h-9 rounded-lg bg-blue-600 flex items-center justify-center shrink-0">
                        <span class="text-white font-bold text-sm">CR</span>
                    </div>
                    <span class="text-white font-semibold text-lg">CampusReserve</span>
                </div>

                <!-- Nav Links -->
                <nav class="flex-1 px-3 py-6 space-y-1">
                    <!-- Dashboard -->
                    <a href="{{ route('dashboard') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                              {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span>Dashboard</span>
                    </a>

                    <!-- My Bookings -->
                    <a href="{{ route('bookings') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                              {{ request()->routeIs('bookings') ? 'bg-blue-600 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span>My Bookings</span>
                    </a>

                    <!-- Profile -->
                    <a href="{{ route('profile') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                              {{ request()->routeIs('profile') ? 'bg-blue-600 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span>Profile</span>
                    </a>

                    <!-- Admin Panel Link (Only visible to admin users) -->
                    @if(auth()->check() && auth()->user()->is_admin)
                        <a href="{{ route('admin.dashboard') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                                  {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            <span>Admin Panel</span>
                        </a>
                    @endif
                </nav>

                <!-- Bottom: Logout -->
                <div class="px-3 py-5 border-t border-slate-800">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center gap-3 px-3 py-2.5 w-full rounded-lg text-sm font-medium text-slate-400 hover:text-white hover:bg-slate-800 transition-colors">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            <span>Log Out</span>
                        </button>
                    </form>
                </div>
            </aside>

            <!-- ===================== MAIN CONTENT ===================== -->
            <div class="flex-1 flex flex-col min-w-0 md:ml-64">

                <!-- Mobile Top Bar -->
                <div class="md:hidden flex items-center gap-3 px-4 py-4 bg-white border-b border-gray-200">
                    <button @click="mobileOpen = !mobileOpen" class="text-slate-600 hover:text-slate-900 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-md bg-blue-600 flex items-center justify-center">
                            <span class="text-white font-bold text-xs">CR</span>
                        </div>
                        <span class="font-semibold text-slate-800">CampusReserve</span>
                    </div>
                </div>

                <!-- Page Content -->
                <main class="flex-1">
                    {{ $slot }}
                </main>
            </div>

        </div>

        @stack('modals')

        @livewireScripts
    </body>
</html>


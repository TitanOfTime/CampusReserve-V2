<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'CampusReserve') }} - Study Spot Booking</title>

        <!-- Inline SVG Favicon -->
        <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%232563eb'%3E%3Crect width='24' height='24' rx='6' fill='%232563eb'/%3E%3Ctext x='12' y='16' font-size='11' font-weight='bold' font-family='sans-serif' fill='white' text-anchor='middle'%3ECR%3C/text%3E%3C/svg%3E">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900&display=swap" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body { font-family: 'Inter', sans-serif; }
        </style>
        <script>
            document.documentElement.setAttribute('data-theme', localStorage.getItem('campus-theme') || 'aurora');
        </script>
    </head>
    <body class="theme-shell text-[color:var(--text)] antialiased min-h-screen flex flex-col">

        <!-- ==================== NAVBAR ==================== -->
        <header class="w-full border-b border-[color:var(--border)] bg-[color:var(--panel)] backdrop-blur-md sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
                
                {{-- Logo / Branding --}}
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl theme-gradient sheen flex items-center justify-center shrink-0 shadow-sm">
                        <span class="text-white font-black text-sm">CR</span>
                    </div>
                    <span class="text-[color:var(--text)] font-black text-xl tracking-tight">CampusReserve</span>
                </div>

                {{-- Action Links --}}
                @if (Route::has('login'))
                    <nav class="flex items-center gap-6">
                        @auth
                            <a href="{{ route('dashboard') }}"
                               class="px-5 py-2.5 theme-button text-white rounded-lg text-sm font-bold shadow transition-colors">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                               class="text-sm font-bold text-[color:var(--muted)] hover:text-[color:var(--text)] transition-colors">
                                Sign In
                            </a>

                            @if (Route::has('register'))
                                <a href="{{ route('register') }}"
                                   class="px-5 py-2.5 theme-button text-white rounded-lg text-sm font-bold shadow transition-colors">
                                    Get Started
                                </a>
                            @endif
                        @endauth
                    </nav>
                @endif

            </div>
        </header>

        <!-- ==================== HERO AREA ==================== -->
        <main class="flex-1 flex flex-col justify-center py-16 md:py-24">
            <div class="max-w-5xl mx-auto px-6 text-center">
                
                {{-- Centered Typography --}}
                <h1 class="text-4xl sm:text-5xl md:text-6xl font-black text-[color:var(--text)] tracking-tight leading-none mb-6">
                    Find your perfect study environment.
                </h1>
                
                <p class="text-lg md:text-xl text-[color:var(--muted)] max-w-3xl mx-auto mb-10 leading-relaxed font-semibold">
                    Stop wandering the halls. Reserve soundproof pods, collaboration tables, and quiet desks instantly.
                </p>

                {{-- CTAs Stack --}}
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4 max-w-md mx-auto mb-16">
                    @auth
                        <a href="{{ route('dashboard') }}"
                           class="w-full sm:flex-1 py-4 px-6 theme-button text-white font-black rounded-xl shadow-lg transition-all text-center tracking-wider text-xs uppercase">
                            Go to App Grid
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                           class="w-full sm:flex-1 py-4 px-6 theme-button text-white font-black rounded-xl shadow-lg transition-all text-center tracking-wider text-xs uppercase">
                            Book a Room
                        </a>
                        <a href="{{ route('register') }}"
                           class="w-full sm:flex-1 py-4 px-6 theme-card border border-[color:var(--border)] text-[color:var(--text)] font-black rounded-xl shadow-sm hover:shadow transition-all text-center tracking-wider text-xs uppercase bg-white/70">
                            Learn More
                        </a>
                    @endauth
                </div>

                {{-- Large Interactive Hero Photo --}}
                <div class="max-w-4xl mx-auto rounded-3xl overflow-hidden shadow-2xl border border-[color:var(--border)] relative group">
                    <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=1200&q=80" 
                         alt="Students working collaboratively" 
                         class="w-full h-auto object-cover transform scale-100 group-hover:scale-[1.01] transition-transform duration-700">
                    <div class="absolute inset-0 bg-gradient-to-t from-[color:var(--sidebar-start)]/30 to-transparent pointer-events-none mix-blend-multiply"></div>
                </div>

            </div>
        </main>

        <!-- ==================== FOOTER ==================== -->
        <footer class="py-8 border-t border-[color:var(--border)] bg-[color:var(--panel)]">
            <div class="max-w-7xl mx-auto px-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs font-bold text-[color:var(--muted)]">
                <p>&copy; {{ date('Y') }} CampusReserve. All rights reserved.</p>
                <div class="flex items-center gap-6">
                    <a href="#" class="hover:text-[color:var(--text)] transition-colors">Privacy Policy</a>
                    <a href="#" class="hover:text-[color:var(--text)] transition-colors">Terms of Service</a>
                </div>
            </div>
        </footer>

    </body>
</html>

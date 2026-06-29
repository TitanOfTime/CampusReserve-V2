<x-guest-layout>
    <div class="min-h-screen flex">
        <!-- Left Side: Hero Image with Theme Blend -->
        <div class="hidden lg:block lg:w-1/2 relative overflow-hidden">
            <img src="https://images.unsplash.com/photo-1541339907198-e08756dedf3f?w=1200&q=80" alt="Campus Library" class="absolute inset-0 w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-[color:var(--sidebar-start)] via-[color:var(--sidebar-end)]/70 to-transparent mix-blend-multiply opacity-90"></div>
            <div class="absolute bottom-0 left-0 p-12 text-white z-10">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl theme-gradient sheen flex items-center justify-center">
                        <span class="text-white font-black text-base">CR</span>
                    </div>
                    <span class="text-2xl font-black tracking-tight">CampusReserve</span>
                </div>
                <p class="text-lg opacity-85 leading-relaxed font-medium">Join the community. Reserve your spot.</p>
            </div>
        </div>

        <!-- Right Side: Auth Form with Glassmorphism -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 sm:p-12 bg-white/20 backdrop-blur-lg border-l border-[color:var(--border)]">
            <div class="w-full max-w-md theme-card border rounded-2xl p-8 sm:p-10 shadow-2xl motion-card">
                <div class="mb-8 text-center sm:text-left">
                    <h2 class="text-3xl font-black tracking-tight text-[color:var(--text)]">Create an Account</h2>
                    <p class="text-[color:var(--muted)] mt-2 text-sm font-medium">Get started with CampusReserve</p>
                </div>

                <x-validation-errors class="mb-4" />

                <!-- Google OAuth Button -->
                <a href="{{ route('google.redirect') }}" class="w-full flex items-center justify-center px-4 py-3 border border-[color:var(--border)] rounded-xl shadow-sm bg-white/90 text-sm font-bold text-[color:var(--text)] hover:bg-white hover:shadow-lg transition-all mb-6">
                    <svg class="h-5 w-5 mr-3" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4" />
                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853" />
                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05" />
                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335" />
                        <path d="M1 1h22v22H1z" fill="none" />
                    </svg>
                    Sign up with Google
                </a>

                <!-- Divider -->
                <div class="relative mb-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-[color:var(--border)]"></div>
                    </div>
                    <div class="relative flex justify-center text-xs font-bold uppercase tracking-wider">
                        <span class="px-3 bg-[color:var(--panel-strong)] text-[color:var(--muted)]">Or continue with email</span>
                    </div>
                </div>

                <form method="POST" action="{{ route('register') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label for="name" class="block text-xs font-bold text-[color:var(--text)] uppercase tracking-wider mb-1.5">{{ __('Name') }}</label>
                        <input id="name" class="block w-full border-[color:var(--border)] bg-white/80 rounded-xl shadow-sm focus:border-[color:var(--accent)] focus:ring-[color:var(--ring)] text-sm py-2" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" />
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-bold text-[color:var(--text)] uppercase tracking-wider mb-1.5">{{ __('Email') }}</label>
                        <input id="email" class="block w-full border-[color:var(--border)] bg-white/80 rounded-xl shadow-sm focus:border-[color:var(--accent)] focus:ring-[color:var(--ring)] text-sm py-2" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" />
                    </div>

                    <div>
                        <label for="password" class="block text-xs font-bold text-[color:var(--text)] uppercase tracking-wider mb-1.5">{{ __('Password') }}</label>
                        <input id="password" class="block w-full border-[color:var(--border)] bg-white/80 rounded-xl shadow-sm focus:border-[color:var(--accent)] focus:ring-[color:var(--ring)] text-sm py-2" type="password" name="password" required autocomplete="new-password" />
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold text-[color:var(--text)] uppercase tracking-wider mb-1.5">{{ __('Confirm Password') }}</label>
                        <input id="password_confirmation" class="block w-full border-[color:var(--border)] bg-white/80 rounded-xl shadow-sm focus:border-[color:var(--accent)] focus:ring-[color:var(--ring)] text-sm py-2" type="password" name="password_confirmation" required autocomplete="new-password" />
                    </div>

                    @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="terms" name="terms" type="checkbox" required class="focus:ring-[color:var(--ring)] h-4 w-4 text-[color:var(--accent)] border-[color:var(--border)] rounded">
                            </div>
                            <div class="ml-2 text-sm font-bold text-[color:var(--muted)]">
                                <label for="terms">
                                    I agree to the <a target="_blank" href="{{ route('terms.show') }}" class="underline hover:text-[color:var(--text)]">Terms of Service</a> and <a target="_blank" href="{{ route('policy.show') }}" class="underline hover:text-[color:var(--text)]">Privacy Policy</a>
                                </label>
                            </div>
                        </div>
                    @endif

                    <div class="pt-2">
                        <button type="submit" class="w-full flex justify-center py-3 px-4 rounded-xl text-sm font-black theme-button transition focus:ring-4 focus:ring-[color:var(--ring)]">
                            {{ __('Register') }}
                        </button>
                    </div>
                </form>

                <div class="mt-6 text-center text-sm text-[color:var(--muted)] font-medium">
                    Already have an account? 
                    <a href="{{ route('login') }}" class="font-bold text-[color:var(--accent)] hover:text-[color:var(--accent-2)] transition">Log in</a>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>

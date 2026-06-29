<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-900">Profile</h1>
        <p class="mt-2 text-slate-600">Your account details and membership status.</p>
    </div>

    {{-- User Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

        {{-- Header Banner --}}
        <div class="h-24 bg-gradient-to-r from-blue-600 to-purple-600"></div>

        {{-- Avatar + Info --}}
        <div class="px-8 pb-8">
            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between -mt-12 mb-6">
                {{-- Avatar --}}
                <div class="w-20 h-20 rounded-2xl ring-4 ring-white overflow-hidden bg-slate-200 shrink-0">
                    @if($user->profile_photo_url)
                        <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-blue-600 text-white text-2xl font-bold">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                </div>

                {{-- Premium / Standard Badge --}}
                <div class="mt-4 sm:mt-0">
                    @if($user->is_premium)
                        <span class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-sm font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                            👑 Premium Member
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-sm font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                            Standard Member
                        </span>
                    @endif
                </div>
            </div>

            {{-- Name & Email --}}
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-slate-900">{{ $user->name }}</h2>
                <p class="text-slate-500 mt-0.5">{{ $user->email }}</p>
                <p class="text-xs text-slate-400 mt-2">
                    Member since {{ $user->created_at->format('F Y') }}
                </p>
            </div>

            {{-- Details Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
                <div class="bg-slate-50 rounded-xl p-4">
                    <p class="text-xs text-slate-500 uppercase tracking-wide font-medium mb-1">Account Type</p>
                    <p class="text-slate-800 font-semibold">{{ $user->is_premium ? 'Premium' : 'Standard' }}</p>
                </div>
                <div class="bg-slate-50 rounded-xl p-4">
                    <p class="text-xs text-slate-500 uppercase tracking-wide font-medium mb-1">Role</p>
                    <p class="text-slate-800 font-semibold">{{ $user->is_admin ? 'Administrator' : 'Student' }}</p>
                </div>
            </div>

            @if(!$user->is_premium)
                <div class="mb-8 p-6 rounded-2xl bg-gradient-to-r from-amber-500 to-orange-600 text-white relative overflow-hidden shadow-md">
                    <div class="relative z-10">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-2xl">👑</span>
                            <h3 class="text-lg font-bold">Upgrade to CampusReserve Premium</h3>
                        </div>
                        <p class="text-amber-100 text-xs mb-4 max-w-md">Get instant access to premium meeting rooms, elite boardrooms, priority scheduling, and unlimited booking durations for a one-time payment of $10.</p>
                        <a href="{{ route('premium.checkout') }}"
                           class="inline-flex items-center px-4 py-2.5 bg-white text-orange-600 text-xs font-bold rounded-lg shadow-sm hover:bg-orange-50 transition-colors uppercase tracking-wider">
                            Upgrade Now - $10
                        </a>
                    </div>
                    {{-- Decorative big background crown --}}
                    <div class="absolute right-0 bottom-0 text-white/10 text-9xl transform translate-x-8 translate-y-8 select-none pointer-events-none font-bold">👑</div>
                </div>
            @endif

            {{-- Log Out Button --}}
            <div class="pt-6 border-t border-gray-100">
                <button wire:click="logout"
                        wire:confirm="Are you sure you want to log out?"
                        class="flex items-center gap-2 px-5 py-2.5 bg-red-50 border border-red-200 text-red-600 text-sm font-semibold rounded-lg hover:bg-red-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Log Out
                </button>
            </div>

        </div>
    </div>

    {{-- Two Factor Authentication Section --}}
    @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 mt-8">
            <h3 class="text-lg font-bold text-slate-900 mb-2">Two-Factor Authentication</h3>
            <p class="text-slate-600 text-sm mb-6">Add additional security to your account using two-factor authentication.</p>
            
            <div class="mt-4">
                @livewire('profile.two-factor-authentication-form')
            </div>
        </div>
    @endif

</div>

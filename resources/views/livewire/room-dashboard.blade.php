@php
    $rooms = $this->rooms;
    $recommendations = collect($recommendation['recommendations'] ?? []);
    $recommendedIds = $recommendations->pluck('room_id')->all();
    $recommendationByRoom = $recommendations->keyBy('room_id');
@endphp

<div class="relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-12 space-y-8">
        
        <!-- ==================== HEADER CARD ==================== -->
        <div class="theme-card border rounded-2xl p-6 sm:p-8 overflow-hidden motion-card">
            <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
                <div class="max-w-2xl">
                    <div class="inline-flex items-center gap-2 theme-subtle rounded-full px-3 py-1 text-xs font-bold uppercase tracking-[0.18em]">
                        <span class="size-2 rounded-full theme-gradient"></span>
                        Campus live grid
                    </div>
                    <h1 class="mt-5 text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight text-[color:var(--text)]">
                        Find Your Study Spot
                    </h1>
                    <p class="mt-3 text-base sm:text-lg text-[color:var(--muted)] max-w-2xl">
                        Reserve quiet desks, collaboration rooms, and premium focus spaces with faster matching and cleaner booking flow.
                    </p>
                </div>

                <div class="grid grid-cols-3 gap-3 min-w-full sm:min-w-[420px] lg:min-w-[360px]">
                    <div class="theme-card-strong border rounded-xl p-4">
                        <p class="text-xs font-semibold text-[color:var(--muted)]">Rooms</p>
                        <p class="mt-1 text-2xl font-black">{{ $rooms->count() }}</p>
                    </div>
                    <div class="theme-card-strong border rounded-xl p-4">
                        <p class="text-xs font-semibold text-[color:var(--muted)]">Premium</p>
                        <p class="mt-1 text-2xl font-black">{{ $rooms->where('is_premium', true)->count() }}</p>
                    </div>
                    <div class="theme-card-strong border rounded-xl p-4">
                        <p class="text-xs font-semibold text-[color:var(--muted)]">Access</p>
                        <p class="mt-1 text-sm font-black truncate">{{ auth()->user()->is_premium ? 'Premium' : 'Standard' }}</p>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200 motion-card">
                {{ session('success') }}
            </div>
        @endif

        @if(session('warning'))
            <div class="p-4 rounded-xl bg-amber-50 text-amber-700 border border-amber-200 motion-card">
                {{ session('warning') }}
            </div>
        @endif

        @if(!auth()->user()->is_premium)
            <div class="theme-gradient sheen rounded-2xl p-6 shadow-lg text-white flex flex-col sm:flex-row items-start sm:items-center justify-between gap-5 motion-card">
                <div>
                    <h3 class="text-xl font-black">Unlock premium rooms</h3>
                    <p class="mt-1 text-white/85">Boardrooms, soundproof pods, and priority-ready spaces become available after upgrade.</p>
                </div>
                <a href="{{ route('premium.checkout') }}" class="px-5 py-2.5 bg-white text-slate-900 font-bold rounded-lg shadow-sm hover:bg-slate-50 transition text-center">
                    Upgrade
                </a>
            </div>
        @endif

        <!-- ==================== DYNAMIC AI WORKSPACE (FULL-WIDTH) ==================== -->
        <section class="theme-card border rounded-2xl p-6 sm:p-8 motion-card">
            <div class="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-8 items-start">
                
                {{-- Form Prompt Area --}}
                <form wire:submit.prevent="suggestRooms" class="space-y-4">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-xs font-black uppercase tracking-[0.2em] text-[color:var(--accent)]">Smart Assistant</p>
                            <h2 class="mt-1 text-2xl font-black tracking-tight">Room Fit Assistant</h2>
                        </div>
                        <div class="size-11 rounded-xl theme-gradient sheen flex items-center justify-center text-white font-black shrink-0">AI</div>
                    </div>
                    
                    <p class="text-sm leading-6 text-[color:var(--muted)]">
                        Describe what you need (e.g. quiet space, group project, presentation setup) and let Gemini AI match you with the best room.
                    </p>

                    <div class="relative">
                        <textarea wire:model="preference"
                                  rows="3"
                                  maxlength="500"
                                  placeholder="Quiet solo focus for two hours, or group presentation with a screen..."
                                  class="w-full rounded-xl border-[color:var(--border)] bg-white/85 text-sm focus:border-[color:var(--accent)] focus:ring-[color:var(--ring)] pr-4 py-3 text-[color:var(--text)]"></textarea>
                        @error('preference') <span class="text-red-500 text-xs mt-2 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex gap-3">
                        <button type="submit"
                                wire:loading.attr="disabled"
                                wire:target="suggestRooms"
                                class="theme-button disabled:opacity-60 rounded-lg px-6 py-3 font-bold transition flex items-center gap-2">
                            <span wire:loading.remove wire:target="suggestRooms">Match Rooms</span>
                            <span wire:loading wire:target="suggestRooms" style="display:none;">Matching...</span>
                        </button>
                        @if($recommendation)
                            <button type="button"
                                    wire:click="clearRecommendation"
                                    class="px-5 py-3 rounded-lg border border-[color:var(--border)] text-sm font-bold hover:bg-white/70 transition">
                                Clear
                            </button>
                        @endif
                    </div>
                </form>

                {{-- Interactive Suggestions Sidebar Inside Card --}}
                <div class="space-y-4 border-t lg:border-t-0 lg:border-l border-[color:var(--border)] pt-6 lg:pt-0 lg:pl-8">
                    <p class="text-xs font-black uppercase tracking-[0.15em] text-[color:var(--muted)]">Quick Suggestions</p>
                    <div class="flex flex-wrap lg:flex-col gap-2">
                        @foreach([
                            'Quiet individual space near library' => '📚 Quiet Study Desk',
                            'Group presentation room with a screen' => '🖥️ Presentation Setup',
                            'Soundproof space for calls' => '🎙️ Soundproof Pod',
                            'Large conference setup' => '👥 Large Meeting'
                        ] as $prompt => $label)
                            <button type="button"
                                    wire:click="$set('preference', '{{ $prompt }}')"
                                    class="text-left px-3 py-2 rounded-xl text-xs font-bold border border-[color:var(--border)] bg-white/60 text-[color:var(--text)] hover:border-[color:var(--accent)] hover:bg-white transition flex items-center justify-between group">
                                <span>{{ $label }}</span>
                                <svg class="w-3.5 h-3.5 opacity-0 group-hover:opacity-100 transition-opacity text-[color:var(--accent)] ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        @endforeach
                    </div>
                </div>

            </div>
        </section>

        <!-- ==================== RECOMMENDATIONS SECTION ==================== -->
        @if($recommendation)
            <section class="space-y-4 motion-card">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-black tracking-tight text-[color:var(--text)]">AI Recommended Spots</h2>
                        <p class="text-sm text-[color:var(--muted)] mt-1">{{ $recommendation['summary'] }}</p>
                    </div>
                    <span class="theme-subtle rounded-full px-4 py-1.5 text-xs font-black uppercase tracking-wider shrink-0 flex items-center gap-1.5">
                        <span class="size-1.5 rounded-full theme-gradient"></span>
                        AI Pick Active
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($recommendations as $match)
                        @php $room = $rooms->firstWhere('id', $match['room_id']); @endphp
                        @if($room)
                            <article wire:key="rec-{{ $room->id }}"
                                     class="theme-card-strong border rounded-2xl overflow-hidden hover:-translate-y-1 hover:shadow-2xl transition duration-300 flex flex-col group ring-2 ring-[color:var(--accent)] bg-gradient-to-b from-white to-[color:var(--accent-soft)]">
                                <div class="h-48 overflow-hidden relative">
                                    <img src="{{ $room->image_url ?: 'https://images.unsplash.com/photo-1497366754035-f200968a6e72?w=900&q=80' }}"
                                         alt="{{ $room->name }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                                    <div class="absolute inset-x-0 bottom-0 h-20 bg-gradient-to-t from-slate-950/70 to-transparent"></div>
                                    <div class="absolute top-4 left-4 flex flex-wrap gap-2">
                                        <span class="rounded-full bg-[color:var(--accent)] text-white text-xs font-black px-3 py-1 shadow-sm">AI Match</span>
                                        @if($room->is_premium)
                                            <span class="rounded-full bg-slate-950/80 text-white text-xs font-bold px-3 py-1">Premium</span>
                                        @endif
                                    </div>
                                    <div class="absolute bottom-4 left-4 right-4 text-white">
                                        <p class="text-xs uppercase tracking-[0.18em] font-bold text-white/75">{{ $room->location }}</p>
                                        <h3 class="mt-1 text-xl font-black leading-tight">{{ $room->name }}</h3>
                                    </div>
                                </div>

                                <div class="p-5 flex flex-col flex-1">
                                    <div class="flex items-center gap-3 text-sm text-[color:var(--muted)]">
                                        <span class="inline-flex items-center gap-1.5">
                                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m0-4a4 4 0 110-8 4 4 0 010 8zm8 0a4 4 0 100-8 4 4 0 000 8z"/></svg>
                                            {{ $room->capacity }} seats
                                        </span>
                                        <span class="inline-flex items-center gap-1.5">
                                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            Instant check
                                        </span>
                                    </div>

                                    <div class="mt-4 rounded-xl border border-[color:var(--accent)]/20 bg-white/75 p-3 text-xs text-[color:var(--text)] flex-1">
                                        <span class="font-bold text-[color:var(--accent)] block mb-1">Why this fits:</span> 
                                        {{ $match['reason'] }}
                                    </div>

                                    <button wire:click="openModal({{ $room->id }})"
                                            wire:loading.attr="disabled"
                                            class="mt-4 w-full theme-button disabled:opacity-60 rounded-lg py-2.5 text-sm font-bold transition flex justify-center items-center">
                                        Book Room
                                    </button>
                                </div>
                            </article>
                        @endif
                    @endforeach
                </div>
            </section>
        @endif

        <!-- ==================== ALL ROOMS GRID (FULL-WIDTH) ==================== -->
        <section class="space-y-4">
            <h2 class="text-2xl font-black tracking-tight text-[color:var(--text)]">All Available Study Spaces</h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($rooms as $room)
                    @php
                        $isRecommended = in_array($room->id, $recommendedIds, true);
                    @endphp

                    <article wire:key="room-{{ $room->id }}"
                             class="theme-card-strong border rounded-2xl overflow-hidden hover:-translate-y-1 hover:shadow-2xl transition duration-300 flex flex-col group motion-card {{ $isRecommended ? 'ring-2 ring-[color:var(--accent)]' : '' }}">
                        <div class="h-48 overflow-hidden relative">
                            <img src="{{ $room->image_url ?: 'https://images.unsplash.com/photo-1497366754035-f200968a6e72?w=900&q=80' }}"
                                 alt="{{ $room->name }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                            <div class="absolute inset-x-0 bottom-0 h-20 bg-gradient-to-t from-slate-950/70 to-transparent"></div>
                            <div class="absolute top-4 left-4 flex flex-wrap gap-2">
                                @if($isRecommended)
                                    <span class="rounded-full bg-white text-slate-950 text-xs font-black px-3 py-1 shadow-sm">AI Recommend</span>
                                @endif
                                @if($room->is_premium)
                                    <span class="rounded-full bg-slate-950/80 text-white text-xs font-bold px-3 py-1">Premium</span>
                                @endif
                            </div>
                            <div class="absolute bottom-4 left-4 right-4 text-white">
                                <p class="text-xs uppercase tracking-[0.18em] font-bold text-white/75">{{ $room->location }}</p>
                                <h3 class="mt-1 text-xl font-black leading-tight">{{ $room->name }}</h3>
                            </div>
                        </div>

                        <div class="p-5 flex flex-col flex-1">
                            <div class="flex items-center gap-3 text-sm text-[color:var(--muted)]">
                                <span class="inline-flex items-center gap-1.5">
                                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m0-4a4 4 0 110-8 4 4 0 010 8zm8 0a4 4 0 100-8 4 4 0 000 8z"/></svg>
                                    {{ $room->capacity }} seats
                                </span>
                                <span class="inline-flex items-center gap-1.5">
                                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Instant check
                                </span>
                            </div>

                            <p class="mt-4 text-sm leading-6 text-[color:var(--muted)] flex-1">{{ $room->description }}</p>

                            <button wire:click="openModal({{ $room->id }})"
                                    wire:loading.attr="disabled"
                                    class="mt-5 w-full theme-button disabled:opacity-60 rounded-lg py-3 font-bold transition focus:ring-4 focus:ring-[color:var(--ring)] flex justify-center items-center">
                                Book Room
                            </button>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

    </div>

    <!-- ==================== BOOKING MODAL ==================== -->
    <div x-data="{ show: @entangle('showModal') }"
         x-show="show"
         x-on:keydown.escape.window="show = false; $wire.closeModal()"
         style="display: none;"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm">

        @if($selectedRoom)
            <div x-show="show"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 @click.away="show = false; $wire.closeModal()"
                 class="theme-card-strong rounded-2xl flex flex-col md:flex-row max-w-5xl w-full overflow-hidden shadow-2xl min-h-[500px] max-h-[90vh]">

                <div class="hidden md:block md:w-1/2 relative bg-gray-100">
                    <img src="{{ $selectedRoom->image_url ?: 'https://images.unsplash.com/photo-1497366754035-f200968a6e72?w=900&q=80' }}" alt="{{ $selectedRoom->name }}" class="absolute inset-0 w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/70 via-transparent to-transparent"></div>
                    <div class="absolute bottom-0 left-0 right-0 p-8 text-white">
                        <p class="text-xs uppercase tracking-[0.18em] font-bold text-white/70">{{ $selectedRoom->location }}</p>
                        <h2 class="mt-2 text-3xl font-black">{{ $selectedRoom->name }}</h2>
                    </div>
                </div>

                <div class="w-full md:w-1/2 p-8 lg:p-10 flex flex-col overflow-y-auto">
                    <button wire:click="closeModal" class="flex items-center text-[color:var(--muted)] hover:text-[color:var(--text)] transition-colors mb-6 text-sm font-bold w-max">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        Back to Dashboard
                    </button>

                    <div class="md:hidden mb-6 h-48 rounded-xl overflow-hidden">
                        <img src="{{ $selectedRoom->image_url ?: 'https://images.unsplash.com/photo-1497366754035-f200968a6e72?w=900&q=80' }}" alt="{{ $selectedRoom->name }}" class="w-full h-full object-cover">
                    </div>

                    <div class="flex flex-wrap items-center gap-2 mb-3">
                        <span class="theme-subtle rounded-full px-3 py-1 text-xs font-black">{{ $selectedRoom->capacity }} seats</span>
                        @if($selectedRoom->is_premium)
                            <span class="rounded-full bg-slate-900 text-white px-3 py-1 text-xs font-black">Premium</span>
                        @endif
                    </div>

                    <h2 class="text-2xl font-black text-[color:var(--text)] mb-2">{{ $selectedRoom->name }}</h2>
                    <p class="text-[color:var(--muted)] mb-8 leading-relaxed">{{ $selectedRoom->description }}</p>

                    <h4 class="font-black text-[color:var(--text)] mb-4">Included setup</h4>
                    <div class="grid grid-cols-2 gap-3 mb-8">
                        @foreach(['High-Speed WiFi', 'Power Outlets', 'Display Ready', 'Whiteboard'] as $amenity)
                            <div class="theme-subtle rounded-xl px-3 py-3 text-sm font-bold">
                                {{ $amenity }}
                            </div>
                        @endforeach
                    </div>

                    <h4 class="font-black text-[color:var(--text)] mb-4">Select time</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
                        <div>
                            <label class="block text-xs text-[color:var(--muted)] mb-1 font-bold">Start Time</label>
                            <input type="datetime-local" wire:model="startTime" class="w-full border-[color:var(--border)] rounded-lg shadow-sm focus:ring-[color:var(--ring)] focus:border-[color:var(--accent)] text-sm">
                            @error('startTime') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs text-[color:var(--muted)] mb-1 font-bold">End Time</label>
                            <input type="datetime-local" wire:model="endTime" class="w-full border-[color:var(--border)] rounded-lg shadow-sm focus:ring-[color:var(--ring)] focus:border-[color:var(--accent)] text-sm">
                            @error('endTime') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        @error('room_id')
                            <div class="col-span-1 sm:col-span-2">
                                <div class="flex items-start gap-2 bg-red-50 border border-red-200 text-red-700 rounded-lg p-3">
                                    <svg class="w-4 h-4 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span class="text-sm font-bold">{{ $message }}</span>
                                </div>
                            </div>
                        @enderror
                    </div>

                    <div class="mt-auto">
                        <button wire:click="bookRoom" wire:loading.attr="disabled" class="w-full theme-button disabled:opacity-75 rounded-lg py-3 font-black transition focus:ring-4 focus:ring-[color:var(--ring)] flex justify-center items-center">
                            <span wire:loading.remove wire:target="bookRoom">Confirm Booking</span>
                            <span wire:loading wire:target="bookRoom" style="display: none;">Processing...</span>
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

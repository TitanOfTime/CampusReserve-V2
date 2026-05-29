<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    {{-- Page Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-900">My Bookings</h1>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="mb-6 flex items-center gap-2 p-4 rounded-lg bg-green-50 text-green-700 border border-green-200">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 flex items-center gap-2 p-4 rounded-lg bg-red-50 text-red-700 border border-red-200">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- Booking List --}}
    <div>
        <h2 class="text-base font-semibold text-slate-500 uppercase tracking-wide mb-4">Upcoming Bookings</h2>

        @if($this->bookings->isEmpty())
            {{-- Empty State --}}
            <div class="text-center py-20 bg-white rounded-2xl border border-gray-100">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-100 flex items-center justify-center">
                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-slate-700 mb-1">No upcoming bookings</h3>
                <p class="text-slate-500 text-sm mb-6">You have no confirmed bookings. Head to the dashboard to reserve a room.</p>
                <a href="{{ route('dashboard') }}" class="inline-flex items-center px-5 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    Browse Rooms
                </a>
            </div>
        @else
            <div class="space-y-4">
                @foreach($this->bookings as $booking)
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex flex-col sm:flex-row items-start sm:items-center gap-5">

                        {{-- Room Thumbnail --}}
                        <div class="w-20 h-20 rounded-xl overflow-hidden shrink-0 bg-gray-100">
                            @if($booking->room->image_url)
                                <img src="{{ $booking->room->image_url }}" alt="{{ $booking->room->name }}" class="w-full h-full object-cover">
                            @endif
                        </div>

                        {{-- Booking Details --}}
                        <div class="flex-1 min-w-0">
                            <h3 class="text-base font-bold text-slate-900 mb-2">{{ $booking->room->name }}</h3>
                            <div class="flex items-center gap-1.5 text-slate-500 text-sm mb-1">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                {{ $booking->start_time->format('l, F j, Y') }}
                            </div>
                            <div class="flex items-center gap-1.5 text-slate-500 text-sm">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ $booking->start_time->format('g:i A') }} – {{ $booking->end_time->format('g:i A') }}
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex flex-col gap-2 shrink-0 w-full sm:w-auto">
                            <button wire:click="editBooking({{ $booking->id }})"
                                    class="px-4 py-2 text-sm font-medium border border-gray-300 text-slate-700 rounded-lg hover:bg-gray-50 transition-colors text-center">
                                Edit Time
                            </button>
                            <button wire:click="cancelBooking({{ $booking->id }})"
                                    wire:confirm="Are you sure you want to cancel this booking? This action cannot be undone."
                                    wire:loading.attr="disabled"
                                    wire:target="cancelBooking({{ $booking->id }})"
                                    class="px-4 py-2 text-sm font-medium border border-red-200 text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition-colors text-center disabled:opacity-60">
                                <span wire:loading.remove wire:target="cancelBooking({{ $booking->id }})">Cancel Booking</span>
                                <span wire:loading wire:target="cancelBooking({{ $booking->id }})" style="display:none;">Cancelling...</span>
                            </button>
                        </div>

                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- ======================== EDIT MODAL ======================== --}}
    <div x-data="{ show: @entangle('showEditModal') }"
         x-show="show"
         x-on:keydown.escape.window="show = false; $wire.closeEditModal()"
         style="display:none;"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">

        @if($editingBooking)
            <div x-show="show"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 @click.away="show = false; $wire.closeEditModal()"
                 class="bg-white rounded-2xl flex flex-col md:flex-row max-w-3xl w-full overflow-hidden shadow-2xl min-h-[400px] max-h-[90vh]">

                {{-- Image Side --}}
                <div class="hidden md:block md:w-2/5 relative bg-gray-100">
                    <img src="{{ $editingBooking->room->image_url }}"
                         alt="{{ $editingBooking->room->name }}"
                         class="absolute inset-0 w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/60 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 p-6 text-white">
                        <p class="text-xs font-semibold uppercase tracking-wide opacity-75 mb-1">Editing</p>
                        <h3 class="text-lg font-bold">{{ $editingBooking->room->name }}</h3>
                    </div>
                </div>

                {{-- Form Side --}}
                <div class="w-full md:w-3/5 p-8 flex flex-col overflow-y-auto">

                    <button wire:click="closeEditModal" class="flex items-center text-slate-500 hover:text-slate-800 transition-colors mb-6 text-sm font-medium w-max">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        Back to My Bookings
                    </button>

                    <h2 class="text-xl font-bold text-slate-900 mb-1">Edit Booking</h2>
                    <p class="text-slate-500 text-sm mb-8">Update the start and end time for your reservation.</p>

                    <div class="space-y-5 flex-1">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">New Start Time</label>
                            <input type="datetime-local"
                                   wire:model="newStartTime"
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                            @error('newStartTime')
                                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">New End Time</label>
                            <input type="datetime-local"
                                   wire:model="newEndTime"
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                            @error('newEndTime')
                                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-8">
                        <button wire:click="saveBooking"
                                wire:loading.attr="disabled"
                                wire:target="saveBooking"
                                class="w-full bg-blue-600 hover:bg-blue-700 disabled:opacity-75 text-white rounded-lg py-3 font-semibold transition-colors focus:ring-4 focus:ring-blue-100 flex justify-center items-center">
                            <span wire:loading.remove wire:target="saveBooking">Save Changes</span>
                            <span wire:loading wire:target="saveBooking" style="display:none;">Saving...</span>
                        </button>
                    </div>

                </div>
            </div>
        @endif
    </div>

</div>

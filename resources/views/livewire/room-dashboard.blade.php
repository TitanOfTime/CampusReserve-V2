<div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-slate-900">Find Your Study Spot</h1>
            <p class="mt-2 text-slate-600">Reserve a space that matches your needs</p>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 rounded-lg bg-green-50 text-green-700 border border-green-200">
                {{ session('success') }}
            </div>
        @endif

        @if(!auth()->user()->is_premium)
            <div class="mb-8 rounded-xl bg-gradient-to-r from-purple-600 to-blue-600 p-6 shadow-lg text-white flex flex-col sm:flex-row items-center justify-between">
                <div>
                    <h3 class="text-xl font-bold">Upgrade to Premium</h3>
                    <p class="mt-1 opacity-90">Get access to exclusive boardrooms, soundproof pods, and priority booking.</p>
                </div>
                <button class="mt-4 sm:mt-0 px-6 py-2 bg-white text-purple-700 font-semibold rounded-lg shadow-sm hover:bg-gray-50 transition-colors">
                    Upgrade Now
                </button>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($rooms as $room)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col group">
                    <div class="h-48 overflow-hidden">
                        <img src="{{ $room->image_url }}" alt="{{ $room->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    </div>
                    <div class="p-6 flex flex-col flex-grow">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-xl font-bold text-slate-900">{{ $room->name }}</h3>
                            @if($room->is_premium)
                                <span class="bg-purple-50 text-purple-700 text-xs font-semibold px-3 py-1 rounded-full">Premium</span>
                            @endif
                        </div>
                        <p class="text-sm text-slate-500 mb-4 flex-grow">{{ $room->description }}</p>
                        <button wire:click="openModal({{ $room->id }})" wire:loading.attr="disabled" wire:target="openModal({{ $room->id }})" class="w-full bg-blue-600 hover:bg-blue-700 disabled:opacity-60 text-white rounded-lg py-2.5 font-medium transition-colors focus:ring-4 focus:ring-blue-100 flex justify-center items-center">
                            <span wire:loading.remove wire:target="openModal({{ $room->id }})">Book Now</span>
                            <span wire:loading wire:target="openModal({{ $room->id }})" style="display:none;">Loading...</span>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Booking Modal -->
    <div x-data="{ show: @entangle('showModal') }" 
         x-show="show" 
         x-on:keydown.escape.window="show = false; $wire.closeModal()"
         style="display: none;" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
         
        @if($selectedRoom)
            <div x-show="show" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 @click.away="show = false; $wire.closeModal()"
                 class="bg-white rounded-2xl flex flex-col md:flex-row max-w-5xl w-full overflow-hidden shadow-2xl min-h-[500px] max-h-[90vh]">
                 
                <!-- Image Side -->
                <div class="hidden md:block md:w-1/2 relative bg-gray-100">
                    <img src="{{ $selectedRoom->image_url }}" alt="{{ $selectedRoom->name }}" class="absolute inset-0 w-full h-full object-cover">
                </div>

                <!-- Content Side -->
                <div class="w-full md:w-1/2 p-8 lg:p-10 flex flex-col overflow-y-auto">
                    <button wire:click="closeModal" class="flex items-center text-slate-500 hover:text-slate-800 transition-colors mb-6 text-sm font-medium w-max">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        Back to Dashboard
                    </button>

                    <h2 class="text-2xl font-bold text-slate-900 mb-2">{{ $selectedRoom->name }}</h2>
                    <p class="text-slate-600 mb-8 leading-relaxed">{{ $selectedRoom->description }}</p>

                    <h4 class="font-semibold text-slate-900 mb-4">Amenities</h4>
                    <div class="grid grid-cols-2 gap-y-4 gap-x-2 mb-8">
                        <div class="flex items-center text-slate-600 text-sm">
                            <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center mr-3 text-blue-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.906 14.142 0M1.394 9.393c5.857-5.857 15.355-5.858 21.213 0"></path></svg>
                            </div>
                            High-Speed WiFi
                        </div>
                        <div class="flex items-center text-slate-600 text-sm">
                            <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center mr-3 text-blue-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            </div>
                            Power Outlets
                        </div>
                        <div class="flex items-center text-slate-600 text-sm">
                            <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center mr-3 text-blue-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </div>
                            Monitor
                        </div>
                        <div class="flex items-center text-slate-600 text-sm">
                            <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center mr-3 text-blue-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                            </div>
                            Whiteboard
                        </div>
                    </div>

                    <h4 class="font-semibold text-slate-900 mb-4">Select Time</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
                        <div>
                            <label class="block text-xs text-slate-500 mb-1">Start Time</label>
                            <input type="datetime-local" wire:model="startTime" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                            @error('startTime') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs text-slate-500 mb-1">End Time</label>
                            <input type="datetime-local" wire:model="endTime" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                            @error('endTime') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        @error('room_id')
                            <div class="col-span-1 sm:col-span-2">
                                <div class="flex items-start gap-2 bg-red-50 border border-red-200 text-red-700 rounded-lg p-3">
                                    <svg class="w-4 h-4 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span class="text-sm font-medium">{{ $message }}</span>
                                </div>
                            </div>
                        @enderror
                    </div>

                    <div class="mt-auto">
                        <button wire:click="bookRoom" wire:loading.attr="disabled" class="w-full bg-blue-600 hover:bg-blue-700 disabled:opacity-75 text-white rounded-lg py-3 font-semibold transition-colors focus:ring-4 focus:ring-blue-100 flex justify-center items-center">
                            <span wire:loading.remove wire:target="bookRoom">Confirm Booking</span>
                            <span wire:loading wire:target="bookRoom" style="display: none;">Processing...</span>
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

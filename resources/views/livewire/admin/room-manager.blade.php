<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    {{-- Page Header --}}
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-900">Room Manager</h1>
            <p class="mt-2 text-slate-600 text-sm">Create, edit, and delete campus rooms.</p>
        </div>
    </div>

    {{-- Flash Notifications --}}
    @if(session('success'))
        <div class="mb-6 flex items-center gap-2 p-4 rounded-lg bg-green-50 text-green-700 border border-green-200">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Main 2-Column Grid Layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        
        {{-- Form Column --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 lg:col-span-1">
            <h2 class="text-lg font-bold text-slate-950 mb-1">
                {{ $editingRoomId ? 'Edit Room' : 'Add New Room' }}
            </h2>
            <p class="text-slate-500 text-xs mb-6">
                {{ $editingRoomId ? 'Modify the selected room details.' : 'Create a new room for the reservation system.' }}
            </p>

            <form wire:submit.prevent="saveRoom" class="space-y-4">
                
                {{-- Room Name --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Room Name</label>
                    <input type="text"
                           wire:model="name"
                           placeholder="e.g. Executive Boardroom"
                           class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('name')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Location --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Location</label>
                    <input type="text"
                           wire:model="location"
                           placeholder="e.g. Science Block, 3rd Floor"
                           class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('location')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Capacity --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Capacity (Seats)</label>
                    <input type="number"
                           wire:model="capacity"
                           placeholder="e.g. 15"
                           class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('capacity')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Image URL --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Image URL</label>
                    <input type="url"
                           wire:model="image_url"
                           placeholder="https://unsplash.com/..."
                           class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('image_url')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Premium Room Toggle --}}
                <div class="flex items-center gap-3 py-2">
                    <input type="checkbox"
                           id="is_premium"
                           wire:model="is_premium"
                           class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <label for="is_premium" class="text-sm font-semibold text-gray-700 cursor-pointer selection:bg-transparent">
                        Premium Room (👑 Required)
                    </label>
                    @error('is_premium')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Description --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
                    <textarea wire:model="description"
                              rows="4"
                              placeholder="Describe room amenities, tech stack, etc..."
                              class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                    @error('description')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Actions --}}
                <div class="pt-4 flex items-center gap-3">
                    <button type="submit"
                            wire:loading.attr="disabled"
                            wire:target="saveRoom"
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white rounded-lg py-2.5 text-sm font-semibold transition-colors flex items-center justify-center disabled:opacity-75">
                        <span wire:loading.remove wire:target="saveRoom">
                            {{ $editingRoomId ? 'Update Room' : 'Create Room' }}
                        </span>
                        <span wire:loading wire:target="saveRoom" style="display:none;">Saving...</span>
                    </button>

                    @if($editingRoomId)
                        <button type="button"
                                wire:click="resetForm"
                                class="px-4 py-2.5 text-sm font-medium border border-gray-300 text-slate-700 rounded-lg hover:bg-gray-50 transition-colors">
                            Cancel
                        </button>
                    @endif
                </div>

            </form>
        </div>

        {{-- Table List Column --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden lg:col-span-2">
            
            <div class="p-6 border-b border-gray-50 bg-slate-50/50">
                <h2 class="text-lg font-bold text-slate-900">Campus Rooms ({{ $this->rooms->count() }})</h2>
                <p class="text-slate-500 text-xs mt-1">Select a room to modify it, or perform deletions.</p>
            </div>

            @if($this->rooms->isEmpty())
                <div class="text-center py-20">
                    <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <h3 class="text-slate-700 font-semibold mb-1">No rooms found</h3>
                    <p class="text-slate-400 text-sm">Add one from the left form panel to get started.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 text-left text-sm">
                        <thead class="bg-slate-50 text-slate-600 font-semibold uppercase tracking-wider text-xs">
                            <tr>
                                <th class="px-6 py-4">Room / Location</th>
                                <th class="px-6 py-4">Capacity</th>
                                <th class="px-6 py-4">Access Type</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-slate-700">
                            @foreach($this->rooms as $room)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    
                                    {{-- Room Name/Location/Thumb --}}
                                    <td class="px-6 py-4 flex items-center gap-3">
                                        <div class="w-12 h-12 rounded-lg overflow-hidden shrink-0 bg-slate-100">
                                            @if($room->image_url)
                                                <img src="{{ $room->image_url }}" alt="{{ $room->name }}" class="w-full h-full object-cover">
                                            @endif
                                        </div>
                                        <div>
                                            <p class="font-semibold text-slate-900">{{ $room->name }}</p>
                                            <p class="text-slate-500 text-xs mt-0.5">{{ $room->location }}</p>
                                        </div>
                                    </td>

                                    {{-- Capacity --}}
                                    <td class="px-6 py-4 font-medium text-slate-600">
                                        {{ $room->capacity }} seats
                                    </td>

                                    {{-- Access Type Badge --}}
                                    <td class="px-6 py-4">
                                        @if($room->is_premium)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-100">
                                                👑 Premium
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                                Standard
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Quick Actions --}}
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <button wire:click="editRoom({{ $room->id }})"
                                                    class="p-2 text-slate-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all"
                                                    title="Edit Room">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </button>
                                            <button wire:click="deleteRoom({{ $room->id }})"
                                                    wire:confirm="Are you sure you want to delete this room? All associated bookings will be permanently deleted."
                                                    class="p-2 text-slate-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all"
                                                    title="Delete Room">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </div>
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

        </div>

    </div>

</div>

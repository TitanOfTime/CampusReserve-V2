<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h3 class="text-lg font-bold mb-4">Welcome, Admin!</h3>
                <p>This is the placeholder admin dashboard. From here, you can manage rooms, bookings, and users.</p>
                <!-- Further management UI components to be added here later -->
            </div>
        </div>
    </div>
</x-app-layout>

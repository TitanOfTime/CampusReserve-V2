<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Room;
use Illuminate\Support\Facades\Schema;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Safe truncate to wipe duplicate entries
        Schema::disableForeignKeyConstraints();
        Room::truncate();
        Schema::enableForeignKeyConstraints();

        $rooms = [
            [
                'name' => 'Executive Suite A',
                'location' => 'Building B, Floor 4',
                'capacity' => 12,
                'is_premium' => true,
                'description' => 'A high-end boardroom with a 4K display, comfortable executive seating, and panoramic campus views. Perfect for focused study or important meetings.',
                'image_url' => 'https://images.unsplash.com/photo-1497366216548-37526070297c?w=800&q=80',
            ],
            [
                'name' => 'Library Desk 4B',
                'location' => 'Library, Main Floor',
                'capacity' => 1,
                'is_premium' => false,
                'description' => 'A quiet, individual study desk with power outlets and good lighting. Ideal for solo study sessions.',
                'image_url' => 'https://images.unsplash.com/photo-1521737604893-d14cc237f11d?w=800&q=80',
            ],
            [
                'name' => 'Focus Pod Pro',
                'location' => 'Library, West Wing',
                'capacity' => 2,
                'is_premium' => true,
                'description' => 'A soundproof study pod designed for deep work. Features acoustic panels, ergonomic seating, and dual monitors.',
                'image_url' => 'https://images.unsplash.com/photo-1519389950473-47ba0277781c?w=800&q=80',
            ],
            [
                'name' => 'Collaboration Hub',
                'location' => 'Student Center, Floor 2',
                'capacity' => 6,
                'is_premium' => false,
                'description' => 'An open-plan collaboration space with a large whiteboard and a screen for screen sharing. Great for group projects.',
                'image_url' => 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=800&q=80',
            ],
            [
                'name' => 'Silent Study Alpha',
                'location' => 'Building A, Floor 3',
                'capacity' => 20,
                'is_premium' => false,
                'description' => 'A strict silent study area with individual carrels to minimize distractions.',
                'image_url' => 'https://images.unsplash.com/photo-1568667256549-094345857637?w=800&q=80',
            ],
            [
                'name' => 'Tech Conference Room',
                'location' => 'Computer Science Bldg, Floor 1',
                'capacity' => 10,
                'is_premium' => true,
                'description' => 'Equipped with the latest technology, including a smart board, video conferencing setup, and multiple displays.',
                'image_url' => 'https://images.unsplash.com/photo-1505373877841-8d25f7d46678?w=800&q=80',
            ],
            [
                'name' => 'Skyline Boardroom',
                'location' => 'Tower East, Floor 12',
                'capacity' => 16,
                'is_premium' => true,
                'description' => 'Premium high-altitude meeting space. Premium soundproofing, dynamic lighting controls, and full media hub integration.',
                'image_url' => 'https://images.unsplash.com/photo-1431540015161-0bf868a2d407?w=800&q=80',
            ],
            [
                'name' => 'Greenhouse Study Pod',
                'location' => 'Campus Garden, Ground Floor',
                'capacity' => 2,
                'is_premium' => false,
                'description' => 'A semi-outdoor glass pod surrounded by plants, offering a natural and refreshing space to study.',
                'image_url' => 'https://images.unsplash.com/photo-1544816155-12df9643f363?w=800&q=80',
            ],
            [
                'name' => 'Multimedia Sandbox',
                'location' => 'Media Arts Center, Floor 1',
                'capacity' => 8,
                'is_premium' => true,
                'description' => 'High-end lab with drawing tablets, green screen capabilities, video editing setup, and spatial speakers.',
                'image_url' => 'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?w=800&q=80',
            ],
            [
                'name' => 'Quiet Corner C',
                'location' => 'Science Library, Alcove 2',
                'capacity' => 1,
                'is_premium' => false,
                'description' => 'A tucked-away reading armchair with integrated side-table and ambient lamp, perfect for long reading sessions.',
                'image_url' => 'https://images.unsplash.com/photo-1506880018603-83d5b814b5a6?w=800&q=80',
            ],
            [
                'name' => 'Design Sprint Room',
                'location' => 'Engineering Hall, Wing B',
                'capacity' => 14,
                'is_premium' => true,
                'description' => 'Collaboration space fully stocked with prototyping tools, multiple rolling whiteboards, and a ultra-wide screen.',
                'image_url' => 'https://images.unsplash.com/photo-1531538606174-0f90ff5dce83?w=800&q=80',
            ],
            [
                'name' => 'Open Lounge Desk 12',
                'location' => 'Main Lounge, Zone A',
                'capacity' => 4,
                'is_premium' => false,
                'description' => 'Shared counter-height bar table in the student center. Casual atmosphere with easy access to the cafe.',
                'image_url' => 'https://images.unsplash.com/photo-1527689368864-3a821dbccc34?w=800&q=80',
            ]
        ];

        foreach ($rooms as $room) {
            Room::create($room);
        }

        // Flush cached rooms catalog
        Room::flushCatalogCache();
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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
                'name' => 'Focus Pod Pro',
                'location' => 'Library, West Wing',
                'capacity' => 2,
                'is_premium' => true,
                'description' => 'A soundproof study pod designed for deep work. Features acoustic panels, ergonomic seating, and dual monitors.',
                'image_url' => 'https://images.unsplash.com/photo-1519389950473-47ba0277781c?w=800&q=80',
            ],
            [
                'name' => 'Library Seat 4B',
                'location' => 'Library, Main Floor',
                'capacity' => 1,
                'is_premium' => false,
                'description' => 'A quiet, individual study desk with power outlets and good lighting. Ideal for solo study sessions.',
                'image_url' => 'https://images.unsplash.com/photo-1521737604893-d14cc237f11d?w=800&q=80',
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
        ];

        foreach ($rooms as $room) {
            \App\Models\Room::create($room);
        }
    }
}

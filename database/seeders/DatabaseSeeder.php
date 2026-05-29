<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create a default admin user for testing
        \App\Models\User::factory()->create([
            'name' => 'Test Admin',
            'email' => 'immylance@gmail.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'is_admin' => true,
            'is_premium' => true,
        ]);

        $this->call([
            RoomSeeder::class,
        ]);
    }
}

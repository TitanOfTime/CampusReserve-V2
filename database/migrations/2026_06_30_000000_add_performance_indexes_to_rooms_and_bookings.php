<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->index(['user_id', 'status', 'start_time'], 'bookings_user_status_start_idx');
            $table->index(['room_id', 'status', 'start_time', 'end_time'], 'bookings_room_status_time_idx');
        });

        Schema::table('rooms', function (Blueprint $table) {
            $table->index(['is_premium', 'capacity', 'name'], 'rooms_catalog_filter_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex('bookings_user_status_start_idx');
            $table->dropIndex('bookings_room_status_time_idx');
        });

        Schema::table('rooms', function (Blueprint $table) {
            $table->dropIndex('rooms_catalog_filter_idx');
        });
    }
};

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class RoomController extends Controller
{
    /**
     * Display a listing of the rooms.
     */
    public function index(Request $request)
    {
        $type = $request->string('type')->toString();
        $type = in_array($type, ['premium', 'standard'], true) ? $type : 'all';

        $rooms = Cache::remember("rooms.catalog.{$type}", now()->addMinutes(10), function () use ($type) {
            $query = Room::query()->catalog()->orderedForCatalog();

            if ($type === 'premium') {
                $query->premium();
            } elseif ($type === 'standard') {
                $query->standard();
            }

            return $query->get();
        });

        return response()->json($rooms);
    }

    /**
     * Display the specified room.
     */
    public function show(Room $room)
    {
        return response()->json($room->only([
            'id',
            'name',
            'location',
            'capacity',
            'is_premium',
            'description',
            'image_url',
        ]));
    }
}

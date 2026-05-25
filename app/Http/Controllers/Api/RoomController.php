<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    /**
     * Display a listing of the rooms.
     */
    public function index(Request $request)
    {
        $query = Room::query();

        if ($request->has('type')) {
            if ($request->type === 'premium') {
                $query->premium();
            } else if ($request->type === 'standard') {
                $query->standard();
            }
        }

        return response()->json($query->get());
    }

    /**
     * Display the specified room.
     */
    public function show(Room $room)
    {
        return response()->json($room);
    }
}

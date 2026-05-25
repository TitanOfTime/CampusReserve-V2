<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    public function __construct(private BookingService $bookingService)
    {
    }

    /**
     * Display a listing of the user's bookings.
     */
    public function index(Request $request)
    {
        $bookings = $request->user()->bookings()->with('room')->get();
        return response()->json($bookings);
    }

    /**
     * Store a newly created booking in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'start_time' => 'required|date|after:now',
            'end_time' => 'required|date|after:start_time',
            'purpose' => 'nullable|string|max:255',
        ]);

        try {
            $booking = $this->bookingService->createBooking($request->user(), $validated);
            return response()->json($booking->load('room'), 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    /**
     * Display the specified booking.
     */
    public function show(Request $request, Booking $booking)
    {
        if ($request->user()->id !== $booking->user_id && !$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($booking->load('room'));
    }

    /**
     * Remove the specified booking (cancel).
     */
    public function destroy(Request $request, Booking $booking)
    {
        if ($request->user()->id !== $booking->user_id && !$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $booking->update(['status' => 'cancelled']);

        return response()->json(['message' => 'Booking cancelled successfully']);
    }
}

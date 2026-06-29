<?php

namespace App\Services;

use App\Ai\Agents\RoomRecommendationAgent;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class RoomRecommendationService
{
    /**
     * Recommend rooms using Laravel AI SDK and the Gemini 3.1 Flash Lite model.
     *
     * @param  Collection<int, \App\Models\Room>  $rooms
     */
    public function recommend(User $user, string $preference, Collection $rooms): array
    {
        $preference = trim($preference);

        if ($rooms->isEmpty()) {
            return [
                'source' => 'ai',
                'summary' => 'No rooms are available to recommend yet.',
                'recommendations' => [],
            ];
        }

        $cacheKey = 'room.recommendation.'.sha1($user->id.'|'.$preference.'|'.$this->roomFingerprint($rooms));

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($user, $preference, $rooms) {
            $agent = new RoomRecommendationAgent();

            $response = $agent->prompt(
                json_encode([
                    'user' => [
                        'is_premium' => (bool) $user->is_premium,
                    ],
                    'preference' => $preference,
                    'rooms' => $rooms->map(fn ($room) => [
                        'id' => $room->id,
                        'name' => $room->name,
                        'location' => $room->location,
                        'capacity' => $room->capacity,
                        'is_premium' => (bool) $room->is_premium,
                        'description' => $room->description,
                    ])->values(),
                ], JSON_THROW_ON_ERROR),
                provider: 'gemini',
                model: 'gemini-3.1-flash-lite'
            );

            $payload = $response->toArray();

            return $this->normalizeAiRecommendation($payload, $rooms);
        });
    }

    /**
     * Create a fingerprint for caching.
     */
    private function roomFingerprint(Collection $rooms): string
    {
        return $rooms
            ->map(fn ($room) => $room->id.':'.$room->updated_at?->timestamp)
            ->implode('|');
    }

    /**
     * Normalize the AI recommendation response.
     */
    private function normalizeAiRecommendation(array $payload, Collection $rooms): array
    {
        $validRoomIds = $rooms->pluck('id')->map(fn ($id) => (int) $id);

        $recommendations = collect($payload['recommendations'] ?? [])
            ->map(fn ($item) => [
                'room_id' => (int) ($item['room_id'] ?? 0),
                'reason' => Str::limit((string) ($item['reason'] ?? 'Strong fit for your request.'), 150),
            ])
            ->filter(fn ($item) => $validRoomIds->contains($item['room_id']))
            ->unique('room_id')
            ->take(3)
            ->values()
            ->all();

        return [
            'source' => 'ai',
            'summary' => Str::limit((string) ($payload['summary'] ?? 'AI matched the best available study rooms for your request.'), 180),
            'recommendations' => $recommendations,
        ];
    }
}

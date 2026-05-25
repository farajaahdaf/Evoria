<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Services\WaitingRoomService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WaitingRoomController extends Controller
{
    public function __construct(private readonly WaitingRoomService $waitingRoom)
    {
    }

    public function join(Request $request, int $eventId): JsonResponse
    {
        $this->ensurePublished($eventId);

        return response()->json(
            $this->waitingRoom->join($eventId, $request->user()->id)
        );
    }

    public function status(Request $request, int $eventId): JsonResponse
    {
        $this->ensurePublished($eventId);

        return response()->json(
            $this->waitingRoom->status($eventId, $request->user()->id)
        );
    }

    public function leave(Request $request, int $eventId): JsonResponse
    {
        $this->waitingRoom->leave($eventId, $request->user()->id);

        return response()->json(['status' => 'left']);
    }

    protected function ensurePublished(int $eventId): void
    {
        $event = Event::query()->select(['id', 'status'])->findOrFail($eventId);

        abort_unless($event->status === 'published', 422, 'Event ini belum tersedia untuk pembelian.');
    }
}

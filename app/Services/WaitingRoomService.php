<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class WaitingRoomService
{
    public function isEnabled(): bool
    {
        return (bool) config('waitingroom.enabled', true);
    }

    /**
     * How many users may hold a checkout slot at the same time (per event).
     * This represents server/DB concurrency capacity — NOT ticket stock.
     * Oversell protection is handled separately by lockForUpdate in BookingController.
     */
    protected function maxActive(): int
    {
        return max(1, (int) config('waitingroom.max_active', 20));
    }

    protected function holdSeconds(): int
    {
        return max(30, (int) config('waitingroom.hold_seconds', 600));
    }

    protected function queueKey(int $eventId): string
    {
        return "wr:q:{$eventId}";
    }

    protected function activeKey(int $eventId): string
    {
        return "wr:a:{$eventId}";
    }

    protected function nowMs(): int
    {
        return (int) (microtime(true) * 1000);
    }

    /**
     * Put the user in line (idempotent) and try to admit immediately.
     */
    public function join(int $eventId, int $userId): array
    {
        if (! $this->isEnabled()) {
            return $this->bypassPayload();
        }

        if (! $this->isAdmitted($eventId, $userId)) {
            // Preserve original join time if already queued. phpredis returns
            // false (not null) for a missing member, so guard with is_numeric.
            if (! is_numeric(Redis::zscore($this->queueKey($eventId), $userId))) {
                Redis::zadd($this->queueKey($eventId), $this->nowMs(), $userId);
            }
            $this->admit($eventId);
        }

        return $this->buildStatus($eventId, $userId);
    }

    /**
     * Poll current status; runs admission so the line keeps moving.
     */
    public function status(int $eventId, int $userId): array
    {
        if (! $this->isEnabled()) {
            return $this->bypassPayload();
        }

        if (! $this->isAdmitted($eventId, $userId)) {
            $this->admit($eventId);
        }

        return $this->buildStatus($eventId, $userId);
    }

    /**
     * Remove the user from line and free any held slot.
     */
    public function leave(int $eventId, int $userId): void
    {
        Redis::zrem($this->queueKey($eventId), $userId);
        Redis::zrem($this->activeKey($eventId), $userId);
        $this->admit($eventId);
    }

    /**
     * Free the checkout slot after a successful booking so the next person moves up.
     */
    public function releaseSlot(int $eventId, int $userId): void
    {
        Redis::zrem($this->activeKey($eventId), $userId);
        Redis::zrem($this->queueKey($eventId), $userId);
        $this->admit($eventId);
    }

    /**
     * True when the user currently holds a non-expired checkout slot.
     */
    public function isAdmitted(int $eventId, int $userId): bool
    {
        if (! $this->isEnabled()) {
            return true;
        }

        $expiry = Redis::zscore($this->activeKey($eventId), $userId);

        return is_numeric($expiry) && (int) $expiry > $this->nowMs();
    }

    /**
     * Move users from the front of the queue into any free active slots.
     * Bounded only by max_active (server concurrency capacity).
     * Oversell protection is the responsibility of BookingController (lockForUpdate).
     */
    protected function admit(int $eventId): void
    {
        Cache::lock("wr:lock:{$eventId}", 3)->get(function () use ($eventId) {
            $now      = $this->nowMs();
            $queueKey = $this->queueKey($eventId);
            $activeKey = $this->activeKey($eventId);

            // Drop expired slots first.
            Redis::zremrangebyscore($activeKey, '-inf', $now);

            $free = $this->maxActive() - (int) Redis::zcard($activeKey);
            if ($free <= 0) {
                return;
            }

            $front = Redis::zrange($queueKey, 0, $free - 1);
            if (empty($front)) {
                return;
            }

            $expiry = $now + ($this->holdSeconds() * 1000);
            foreach ($front as $userId) {
                Redis::zadd($activeKey, $expiry, $userId);
                Redis::zrem($queueKey, $userId);
            }
        });
    }

    protected function buildStatus(int $eventId, int $userId): array
    {
        $now = $this->nowMs();
        $expiry = Redis::zscore($this->activeKey($eventId), $userId);

        if (is_numeric($expiry) && (int) $expiry > $now) {
            return [
                'status' => 'admitted',
                'expires_at' => Carbon::createFromTimestampMs((int) $expiry)->toIso8601String(),
                'seconds_left' => (int) ceil(((int) $expiry - $now) / 1000),
            ];
        }

        $rank = Redis::zrank($this->queueKey($eventId), $userId);

        if ($rank === null || $rank === false) {
            // Neither queued nor admitted: never joined or slot already expired.
            return ['status' => 'expired'];
        }

        $position = (int) $rank + 1;
        $totalWaiting = (int) Redis::zcard($this->queueKey($eventId));

        // Estimate: each batch of max_active users is admitted every hold_seconds.
        // e.g. position 5, max_active 2, hold_seconds 120 → ceil(5/2) * 120 = 360 s
        $batches = (int) ceil($position / $this->maxActive());

        return [
            'status'           => 'waiting',
            'position'         => $position,
            'total_waiting'    => $totalWaiting,
            'estimate_seconds' => $batches * $this->holdSeconds(),
        ];
    }

    protected function bypassPayload(): array
    {
        return [
            'status' => 'admitted',
            'expires_at' => null,
            'seconds_left' => null,
            'bypassed' => true,
        ];
    }
}

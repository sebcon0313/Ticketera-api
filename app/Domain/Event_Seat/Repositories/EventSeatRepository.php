<?php

namespace App\Domain\Event_Seat\Repositories;

use App\Domain\Event\Models\Event;
use App\Domain\Event_Seat\Models\EventSeat;
use App\Domain\Event_Seat\Repositories\Contracts\EventSeatRepositoryInterface;
use Illuminate\Support\Collection;

class EventSeatRepository implements EventSeatRepositoryInterface
{
    public function insertMany(array $rows): bool
    {
        if (empty($rows)) {
            return true;
        }

        return EventSeat::query()->insert($rows);
    }

    public function createManyForEvent(int $eventId, array $seatRows): bool
    {
        foreach ($seatRows as &$row) {
            $row['event_id'] = $eventId;
        }

        unset($row);

        return $this->insertMany($seatRows);
    }

    public function findByEventIdWithSeat(int $eventId): Collection
    {
        return EventSeat::query()
            ->with(['seat.section'])
            ->where('event_id', $eventId)
            ->get();
    }

    public function findAvailableByEventAndSeatIds(int $eventId, array $seatIds): Collection
    {
        return EventSeat::query()
            ->with(['seat'])
            ->where('event_id', $eventId)
            ->whereIn('id', $seatIds)
            ->where('status', 'disponible')
            ->lockForUpdate()
            ->get();
    }

    public function updateStatusByIds(array $ids, string $status): int
    {
        if (empty($ids)) {
            return 0;
        }

        return EventSeat::query()
            ->whereIn('id', $ids)
            ->update([
                'status' => $status,
                'updated_at' => now(),
            ]);
    }

    public function deleteByEventId(int $eventId): int
    {
        return EventSeat::query()
            ->where('event_id', $eventId)
            ->delete();
    }
}
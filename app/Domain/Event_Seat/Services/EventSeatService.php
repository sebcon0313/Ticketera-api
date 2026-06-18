<?php

namespace App\Domain\Event_Seat\Services;

use App\Domain\Event\Models\Event;
use App\Domain\Event_Seat\Repositories\EventSeatRepository;
use App\Domain\Seat\Repositories\SeatRepository;
use Illuminate\Support\Collection;

class EventSeatService
{
    public function __construct(
        private readonly EventSeatRepository $repository,
        private readonly SeatRepository $seatRepository,
    ) {}

    public function generateForEvent(Event $event): int
    {
        $seats = $this->seatRepository->findByVenueId($event->venue_id);

        if ($seats->isEmpty()) {
            return 0;
        }

        $now = now();
        $rows = $seats->map(function ($seat) use ($event, $now): array {
            return [
                'event_id' => $event->id,
                'seat_id' => $seat->id,
                'status' => 'disponible',
                'created_at' => $now,
                'updated_at' => $now,
            ];
        })->all();

        $this->repository->insertMany($rows);

        return count($rows);
    }

    public function seatMapForEvent(int $eventId): array
    {
        $eventSeats = $this->repository->findByEventIdWithSeat($eventId);

        return $eventSeats
            ->groupBy(fn ($eventSeat) => $eventSeat->seat->section_id)
            ->values()
            ->map(function ($group): array {
                $firstSeat = $group->first()->seat;

                return [
                    'section' => $firstSeat->section?->name,
                    'seats' => $group->map(function ($eventSeat): array {
                        return [
                            'id' => $eventSeat->seat->id,
                            'row' => $eventSeat->seat->row_label,
                            'number' => $eventSeat->seat->seat_number,
                            'status' => $eventSeat->status,
                        ];
                    })->values()->all(),
                ];
            })
            ->all();
    }

    public function localitiesByEvent(int $eventId): array
    {
        $eventSeats = $this->repository->findByEventIdWithSeat($eventId);

        return $eventSeats
            ->groupBy(fn ($eventSeat) => $eventSeat->seat->section_id)
            ->values()
            ->map(function (Collection $group): array {
                $firstSeat = $group->first()->seat;

                return [
                    'section_id' => $firstSeat->section?->id,
                    'section' => $firstSeat->section?->name,
                    'code' => $firstSeat->section?->code,
                    'total_seats' => $group->count(),
                ];
            })
            ->all();
    }
}
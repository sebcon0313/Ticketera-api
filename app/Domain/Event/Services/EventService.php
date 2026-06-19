<?php

namespace App\Domain\Event\Services;

use App\Domain\Event\Models\Event;
use App\Domain\Event_Seat\Repositories\EventSeatRepository;
use App\Domain\Event_Seat\Services\EventSeatService;
use App\Domain\Event\Repositories\Contracts\EventRepositoryInterface;
use Illuminate\Support\Facades\DB;

class EventService
{
    public function __construct(
        private readonly EventRepositoryInterface $repository
        , private readonly EventSeatService $eventSeatService
        , private readonly EventSeatRepository $eventSeatRepository
    ) {}

    public function list()
    {
        return $this->repository->paginate();
    }

    public function get(int $id): Event
    {
        $event = $this->repository->findById($id);

        if (!$event) {
            abort(404, 'Event not found');
        }

        return $event;
    }

    public function create(array $data, int $userId): Event
    {
        return DB::transaction(function () use ($data, $userId): Event {
            $data['created_by'] = $userId;

            $event = $this->repository->create($data);
            $this->eventSeatService->generateForEvent($event);

            return $event;
        });
    }

    public function update(Event $event, array $data): Event
    {
        return DB::transaction(function () use ($event, $data): Event {
            $venueChanged = array_key_exists('venue_id', $data) && (int) $data['venue_id'] !== (int) $event->venue_id;

            $updatedEvent = $this->repository->update($event, $data);

            if ($venueChanged) {
                DB::table('event_seats')
                    ->where('event_id', $updatedEvent->id)
                    ->delete();
                $this->eventSeatService->generateForEvent($updatedEvent);
            }

            return $updatedEvent->fresh(['venue', 'organizer']);
        });
    }

    public function delete(Event $event): bool
    {
        return $this->repository->delete($event);
    }

    public function seatMap(int $id): array
    {
        $event = $this->get($id);

        return $this->eventSeatRepository
            ->findByEventIdWithSeat($event->id)
            ->groupBy(fn ($eventSeat) => $eventSeat->seat->section_id)
            ->values()
            ->map(function ($group): array {
                $firstSeat = $group->first()->seat;

                return [
                    'section' => $firstSeat->section?->name,
                    'seats' => $group->map(function ($eventSeat): array {
                        return [
                            'id' => $eventSeat->id,
                            'row' => $eventSeat->seat->row_label,
                            'number' => $eventSeat->seat->seat_number,
                            'status' => $eventSeat->status,
                        ];
                    })->values()->all(),
                ];
            })
            ->all();
    }
}
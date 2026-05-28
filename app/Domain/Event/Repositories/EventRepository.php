<?php

namespace App\Domain\Event\Repositories;

use App\Domain\Event\Models\Event;
use App\Domain\Event\Repositories\Contracts\EventRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EventRepository implements EventRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Event::with('venue')
            ->latest()
            ->paginate($perPage);
    }

    public function findById(int $id): ?Event
    {
        return Event::with('venue')->find($id);
    }

    public function create(array $data): Event
    {
        $event = Event::create($data);

        return $event->load('organizer', 'venue');
    }

    public function update(Event $event, array $data): Event
    {
        $event->update($data);

        return $event->fresh();
    }

    public function delete(Event $event): bool
    {
        return $event->delete();
    }
}
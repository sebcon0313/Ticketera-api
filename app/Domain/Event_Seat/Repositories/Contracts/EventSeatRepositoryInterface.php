<?php

namespace App\Domain\Event_Seat\Repositories\Contracts;

use App\Domain\Event_Seat\Models\EventSeat;
use Illuminate\Support\Collection;

interface EventSeatRepositoryInterface
{
    public function insertMany(array $rows): bool;

    public function createManyForEvent(int $eventId, array $seatRows): bool;

    public function findByEventIdWithSeat(int $eventId): Collection;

    public function deleteByEventId(int $eventId): int;
}
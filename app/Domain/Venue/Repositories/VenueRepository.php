<?php

namespace App\Domain\Venue\Repositories;

use App\Domain\Venue\Models\Venue;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class VenueRepository implements IVenueRepository
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Venue::query()
            ->withCount('events')
            ->latest()
            ->paginate($perPage);
    }

    public function findById(int $id): ?Venue
    {
        return Venue::withCount('events')->find($id);
    }

    public function create(array $data): Venue
    {
        $venue = Venue::create($data);

        return $venue->loadCount('events');
    }

    public function update(Venue $venue, array $data): Venue
    {
        $venue->update($data);

        return $venue->fresh()->loadCount('events');
    }

    public function delete(Venue $venue): bool
    {
        return $venue->delete();
    }
}
<?php

namespace App\Domain\Venue\Services;

use App\Domain\Venue\Models\Venue;
use App\Domain\Venue\Repositories\IVenueRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class VenueService
{
    public function __construct(
        protected IVenueRepository $venueRepository
    ) {}

    public function list(int $perPage = 15): LengthAwarePaginator
    {
        return $this->venueRepository->paginate($perPage);
    }

    public function findById(int $id): Venue
    {
        $venue = $this->venueRepository->findById($id);

        if (! $venue) {
            abort(404, 'Venue not found');
        }

        return $venue;
    }

    public function create(array $data): Venue
    {
        return $this->venueRepository->create($data);
    }

    public function update(Venue $venue, array $data): Venue
    {
        return $this->venueRepository->update($venue, $data);
    }

    public function delete(Venue $venue): bool
    {
        return $this->venueRepository->delete($venue);
    }
}
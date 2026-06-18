<?php

namespace App\Domain\Venue\Repositories;

use App\Domain\Venue\Models\Venue;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface IVenueRepository
{
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function findById(int $id): ?Venue;

    public function create(array $data): Venue;

    public function update(Venue $venue, array $data): Venue;

    public function delete(Venue $venue): bool;
}
<?php

namespace App\Domain\Seat\Repositories;

use App\Domain\Seat\Models\Seat;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface ISeatRepository
{
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    // Buscar por localidad (Section)
    public function findBySection(int $idSection, int $perPage = 15): LengthAwarePaginator;

    public function findById(int $id): ?Seat;

    public function create(array $data): Seat;

    public function update(Seat $seat, array $data): Seat;

    public function delete(Seat $seat): bool;

    public function getExistingSeatKeys(int $sectionId, array $rows, array $seatNumbers): Collection;

    public function insertMany(array $rows): bool;

    public function findByVenueId(int $venueId): Collection;
}
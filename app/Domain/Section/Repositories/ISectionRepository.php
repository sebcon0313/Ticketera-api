<?php

namespace App\Domain\Section\Repositories;

use App\Domain\Section\Models\Section;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface ISectionRepository
{
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function findByVenueId(int $venueId): Collection;

    public function findById(int $id): ?Section;

    public function create(array $data): Section;

    public function update(Section $section, array $data): Section;

    public function delete(Section $section): bool;
}
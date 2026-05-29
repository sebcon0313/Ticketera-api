<?php

namespace App\Domain\Section\Repositories;

use App\Domain\Section\Models\Section;
use App\Domain\Section\Repositories\ISectionRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class SectionRepository implements ISectionRepository
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Section::with('venue')
            ->latest()
            ->paginate($perPage);
    }

    public function findByVenueId(int $venueId): Collection
    {
        return Section::with('venue')
            ->where('venue_id', $venueId)
            ->orderBy('name')
            ->get();
    }

    public function findById(int $id): ?Section
    {
        return Section::with('venue')->find($id);
    }

    public function create(array $data): Section
    {
        $section = Section::create($data);

        return $section->load('venue');
    }

    public function update(Section $section, array $data): Section
    {
        $section->update($data);

        return $section->fresh(['venue']);
    }

    public function delete(Section $section): bool
    {
        return $section->delete();
    }
}
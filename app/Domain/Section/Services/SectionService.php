<?php

namespace App\Domain\Section\Services;

use App\Domain\Section\Models\Section;
use App\Domain\Section\Repositories\ISectionRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class SectionService
{
    protected ISectionRepository $sectionRepository;

    public function __construct(ISectionRepository $sectionRepository)
    {
        $this->sectionRepository = $sectionRepository;
    }

    public function list(int $perPage = 15): LengthAwarePaginator
    {
        return $this->sectionRepository->paginate($perPage);
    }

    public function listByVenueId(int $venueId): Collection
    {
        return $this->sectionRepository->findByVenueId($venueId);
    }

    public function get(int $id): Section
    {
        $section = $this->sectionRepository->findById($id);

        if (!$section) {
            abort(404, 'Section not found');
        }

        return $section;
    }

    public function findById(int $id): ?Section
    {
        $section = $this->sectionRepository->findById($id);

        if (!$section) {
            abort(404, 'Section not found');
        }

        return $section;
    }

    public function create(array $data): Section
    {
        return $this->sectionRepository->create($data);
    }

    public function update(Section $section, array $data): Section
    {
        return $this->sectionRepository->update($section, $data);
    }

    public function delete(Section $section): bool
    {
        return $this->sectionRepository->delete($section);
    }
}
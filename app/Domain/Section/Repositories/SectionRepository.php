<?php

namespace App\Domain\Section\Repositories;

use App\Domain\Section\Models\Section;
use App\Domain\Section\Repositories\ISectionRepository;

class SectionRepository implements ISectionRepository
{
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

        return $section->fresh();
    }

    public function delete(Section $section): bool
    {
        return $section->delete();
    }
}
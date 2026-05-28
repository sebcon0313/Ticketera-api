<?php

namespace App\Domain\Section\Repositories;

use App\Domain\Section\Models\Section;

interface ISectionRepository
{
    public function findById(int $id): ?Section;

    public function create(array $data): Section;

    public function update(Section $section, array $data): Section;

    public function delete(Section $section): bool;
}
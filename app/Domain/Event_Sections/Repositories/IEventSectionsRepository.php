<?php

namespace App\Domain\Event_Sections\Repositories;
use App\Domain\Event_Sections\Models\EventSection;

interface IEventSectionsRepository
{
	public function upsertMany(array $rows): int;

	public function findByEventSectionId(int $eventId, int $sectionId): ?EventSection;	
}

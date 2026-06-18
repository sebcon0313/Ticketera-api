<?php

namespace App\Domain\Event_Sections\Repositories;

use App\Domain\Event_Sections\Models\EventSection;
use App\Domain\Event_Sections\Repositories\IEventSectionsRepository;

class EventSectionsRepository implements IEventSectionsRepository
{
	public function upsertMany(array $rows): int
	{
		if (empty($rows)) {
			return 0;
		}

		return EventSection::query()->upsert(
			$rows,
			['event_id', 'section_id'],
			['price', 'updated_at']
		);
	}
}

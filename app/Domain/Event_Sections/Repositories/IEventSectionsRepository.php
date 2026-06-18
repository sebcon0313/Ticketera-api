<?php

namespace App\Domain\Event_Sections\Repositories;

interface IEventSectionsRepository
{
	public function upsertMany(array $rows): int;
}

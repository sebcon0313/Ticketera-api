<?php

namespace App\Domain\Event_Sections\Services;

use App\Domain\Event\Repositories\Contracts\EventRepositoryInterface;
use App\Domain\Event_Sections\Dtos\EventSectionsDTO;
use App\Domain\Event_Sections\Repositories\IEventSectionsRepository;

class EventSectionsService
{
    public function __construct(
        private readonly IEventSectionsRepository $repository,
        private readonly EventRepositoryInterface $eventRepository,
    ) {}

    /**
     * @param EventSectionsDTO $dto
     */
    public function storePrices($dto): array
    {
        $event = $this->eventRepository->findById($dto->eventId());

        if (! $event) {
            abort(404, 'Event not found');
        }

        $now = now();
        $rows = array_map(static function (array $section) use ($dto, $now): array {
            return [
                'event_id' => $dto->eventId(),
                'section_id' => (int) $section['section_id'],
                'price' => number_format((float) $section['price'], 2, '.', ''),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }, $dto->sections());

        $affectedRows = $this->repository->upsertMany($rows);

        return [
            'event_id' => $dto->eventId(),
            'sections_count' => count($rows),
            'affected_rows' => $affectedRows,
        ];
    }
}
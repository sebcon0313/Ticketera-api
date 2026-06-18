<?php

namespace App\Domain\Event_Sections\Dtos;

class EventSectionsDTO
{
    public function __construct(
        private readonly int $eventId,
        private readonly array $sections,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            (int) $data['event_id'],
            array_values($data['sections'])
        );
    }

    public function eventId(): int
    {
        return $this->eventId;
    }

    public function sections(): array
    {
        return $this->sections;
    }
}
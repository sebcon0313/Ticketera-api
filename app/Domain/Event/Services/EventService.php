<?php

namespace App\Domain\Event\Services;

use App\Domain\Event\models\Event;
use App\Domain\Event\Repositories\Contracts\EventRepositoryInterface;

class EventService
{
    public function __construct(
        private readonly EventRepositoryInterface $repository
    ) {}

    public function list()
    {
        return $this->repository->paginate();
    }

    public function get(int $id): Event
    {
        $event = $this->repository->findById($id);

        if (!$event) {
            abort(404, 'Event not found');
        }

        return $event;
    }

    public function create(array $data, int $userId): Event
    {
        $data['created_by'] = $userId;

        return $this->repository->create($data);
    }

    public function update(Event $event, array $data): Event
    {
        return $this->repository->update($event, $data);
    }

    public function delete(Event $event): bool
    {
        return $this->repository->delete($event);
    }
}
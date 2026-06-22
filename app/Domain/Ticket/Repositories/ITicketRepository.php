<?php 

namespace App\Domain\Ticket\Repositories;

interface ITicketRepository
{
    public function insertMany(array $rows): bool;

    public function updateQrCodes(array $tickets, int $userId): int;

    public function findById(int $id) : ?object;
}
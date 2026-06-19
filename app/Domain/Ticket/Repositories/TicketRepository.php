<?php

namespace App\Domain\Ticket\Repositories;

use Illuminate\Support\Facades\DB;

class TicketRepository
{
    public function insertMany(array $rows): bool
    {
        if (empty($rows)) {
            return true;
        }

        return DB::table('tickets')->insert($rows);
    }
}
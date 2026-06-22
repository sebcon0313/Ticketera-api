<?php

namespace App\Domain\Ticket\Repositories;

use Illuminate\Support\Facades\DB;

class TicketRepository implements ITicketRepository
{
    public function insertMany(array $rows): bool
    {
        if (empty($rows)) {
            return true;
        }

        return DB::table('tickets')->insert($rows);
    }

    public function findById(int $id): ?object
    {
        return DB::table('tickets')
            ->where('id', $id)
            ->first();
    }

    public function updateQrCodes(array $tickets, int $userId): int
    {
        $updatedCount = 0;

        foreach ($tickets as $ticket) {
            $affectedRows = DB::table('tickets')
                ->where('id', $ticket['ticket_id'])
                ->where('user_id', $userId)
                ->update([
                    'qr_code' => $ticket['qr_code'],
                    'updated_at' => now(),
                ]);

            if ($affectedRows > 0) {
                $updatedCount += $affectedRows;
            }
        }

        return $updatedCount;
    }
}
<?php

namespace App\Domain\booking_Seat\Repositories;

use App\Domain\booking_Seat\Models\BookingSeat;
use Illuminate\Support\Collection;

class BookingSeatRepository
{
    public function insertMany(array $rows): bool
    {
        if (empty($rows)) {
            return true;
        }

        return BookingSeat::query()->insert($rows);
    }

    public function findByBookingId(int $bookingId): Collection
    {
        return BookingSeat::query()
            ->where('booking_id', $bookingId)
            ->get();
    }

    public function updateStatusByBookingId(int $bookingId, string $status): int
    {
        return BookingSeat::query()
            ->where('booking_id', $bookingId)
            ->update([
                'status' => $status,
                'updated_at' => now(),
            ]);
    }
}
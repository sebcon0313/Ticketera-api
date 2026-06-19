<?php

namespace App\Domain\Booking\Repositories;

use App\Domain\Booking\Models\Booking;
use Illuminate\Support\Collection;

class BookingRepository
{
    public function create(array $data): Booking
    {
        return Booking::create($data);
    }

    public function findOverdueReservations(): Collection
    {
        return Booking::query()
            ->with('bookingSeats')
            ->where('status', 'reservado')
            ->whereNotNull('reserved_until')
            ->where('reserved_until', '<=', now())
            ->get();
    }

    public function updateStatus(int $bookingId, string $status): int
    {
        return Booking::query()
            ->where('id', $bookingId)
            ->update([
                'status' => $status,
                'updated_at' => now(),
            ]);
    }
}
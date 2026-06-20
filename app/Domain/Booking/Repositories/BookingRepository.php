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

    public function findByBookingIdForUpdate(int $bookingId): ?Booking
    {
        return Booking::query()
            ->with(['bookingSeats.eventSeat.seat', 'payments', 'tickets'])
            ->where('id', $bookingId)
            ->lockForUpdate()
            ->first();
    }

    public function markAsPaid(Booking $booking, array $data): int
    {
        return Booking::query()
            ->where('id', $booking->id)
            ->update([
                'payment_method' => $data['payment_method'],
                'transaction_reference' => $data['transaction_reference'] ?? null,
                'status' => Booking::STATUS_PAID,
                'confirmed_at' => $data['confirmed_at'] ?? now(),
                'updated_at' => now(),
            ]);
    }

    public function markAsCancelled(Booking $booking, array $data): int
    {
        return Booking::query()
            ->where('id', $booking->id)
            ->update([
                'payment_method' => $data['payment_method'] ?? $booking->payment_method,
                'transaction_reference' => $data['transaction_reference'] ?? null,
                'status' => Booking::STATUS_CANCELLED,
                'cancelled_at' => $data['cancelled_at'] ?? now(),
                'updated_at' => now(),
            ]);
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

    public function findSummaryByIdAndUser(int $bookingId, int $userId): ?Booking {
        return Booking::query()
            ->with([
                'user',
                'event',
                'invoice',
                'tickets.bookingSeat.eventSeat.seat.section'
            ])
            ->where('id', $bookingId)
            ->where('user_id', $userId)
            ->first();
    }
}
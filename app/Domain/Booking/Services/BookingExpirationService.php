<?php

namespace App\Domain\Booking\Services;

use App\Domain\Booking\Repositories\BookingRepository;
use App\Domain\Event_Seat\Repositories\EventSeatRepository;
use App\Domain\booking_Seat\Repositories\BookingSeatRepository;
use Illuminate\Support\Facades\DB;

class BookingExpirationService
{
    public function __construct(
        private readonly BookingRepository $bookingRepository,
        private readonly BookingSeatRepository $bookingSeatRepository,
        private readonly EventSeatRepository $eventSeatRepository,
    ) {}

    public function expireOverdueReservations(): int
    {
        $bookings = $this->bookingRepository->findOverdueReservations();
        $expiredCount = 0;

        foreach ($bookings as $booking) {
            if ($booking->status === 'proceso_pago') {
                continue;
            }

            DB::transaction(function () use ($booking, &$expiredCount): void {
                $bookingSeats = $this->bookingSeatRepository->findByBookingId($booking->id);
                $eventSeatIds = $bookingSeats->pluck('event_seat_id')->all();

                $this->bookingSeatRepository->updateStatusByBookingId($booking->id, 'expirado');

                if (! empty($eventSeatIds)) {
                    $this->eventSeatRepository->updateStatusByIds($eventSeatIds, 'disponible');
                }

                $this->bookingRepository->updateStatus($booking->id, 'expirado');

                $expiredCount++;
            });
        }

        return $expiredCount;
    }
}
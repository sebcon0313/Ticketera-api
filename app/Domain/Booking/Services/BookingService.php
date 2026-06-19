<?php

namespace App\Domain\Booking\Services;

use App\Domain\Booking\Repositories\BookingRepository;
use App\Domain\Event_Sections\Models\EventSection;
use App\Domain\Event_Seat\Repositories\EventSeatRepository;
use App\Domain\booking_Seat\Repositories\BookingSeatRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class BookingService
{
    public function __construct(
        private readonly BookingRepository $bookingRepository,
        private readonly BookingSeatRepository $bookingSeatRepository,
        private readonly EventSeatRepository $eventSeatRepository,
    ) {}

    public function reserveSeats(int $userId, int $eventId, array $seatIds): array
    {
        $seatIds = array_values(array_unique(array_map('intval', $seatIds)));

        if (empty($seatIds)) {
            throw ValidationException::withMessages([
                'seat_ids' => ['You must select at least one seat.'],
            ]);
        }

        $now = now();
        $expiresAt = $now->copy()->addMinutes(5);

        return DB::transaction(function () use ($userId, $eventId, $seatIds, $now, $expiresAt): array {
            $eventSeats = $this->eventSeatRepository->findAvailableByEventAndSeatIds($eventId, $seatIds);

            if ($eventSeats->count() !== count($seatIds)) {
                throw ValidationException::withMessages([
                    'seat_ids' => ['One or more seats do not exist, are not part of the event, or are no longer available.'],
                ]);
            }

            $sectionIds = $eventSeats
                ->pluck('seat.section_id')
                ->unique()
                ->values()
                ->all();

            $pricesBySection = EventSection::query()
                ->where('event_id', $eventId)
                ->whereIn('section_id', $sectionIds)
                ->pluck('price', 'section_id');

            $total = 0.0;
            $bookingSeatRows = [];

            foreach ($eventSeats as $eventSeat) {
                $sectionId = (int) $eventSeat->seat->section_id;
                $price = $pricesBySection->get($sectionId);

                if ($price === null) {
                    throw ValidationException::withMessages([
                        'event_id' => ['This event does not have a configured price for one or more seat sections.'],
                    ]);
                }

                $priceValue = (float) $price;
                $total += $priceValue;

                $bookingSeatRows[] = [
                    'event_seat_id' => $eventSeat->id,
                    'price_snapshot' => number_format($priceValue, 2, '.', ''),
                    'hold_expires_at' => $expiresAt,
                    'status' => 'reservado',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            $booking = $this->bookingRepository->create([
                'reference' => $this->generateReference(),
                'user_id' => $userId,
                'event_id' => $eventId,
                'total' => number_format($total, 2, '.', ''),
                'status' => 'reservado',
                'reserved_until' => $expiresAt,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            foreach ($bookingSeatRows as &$row) {
                $row['booking_id'] = $booking->id;
            }
            unset($row);

            $this->bookingSeatRepository->insertMany($bookingSeatRows);
            $this->eventSeatRepository->updateStatusByIds($eventSeats->pluck('id')->all(), 'reservado');

            return [
                'booking_id' => $booking->id,
                'reference' => $booking->reference,
                'total' => (float) $booking->total,
                'expires_at' => $expiresAt->toDateTimeString(),
            ];
        });
    }

    private function generateReference(): string
    {
        return 'BK-' . Str::upper(Str::random(33));
    }
}
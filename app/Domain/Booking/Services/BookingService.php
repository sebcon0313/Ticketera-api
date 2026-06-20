<?php

namespace App\Domain\Booking\Services;

use App\Domain\Booking\Repositories\BookingRepository;
use App\Domain\Event_Sections\Models\EventSection;
use App\Domain\Event_Seat\Repositories\EventSeatRepository;
use App\Domain\Payments\Repositories\PaymentRepository;
use App\Domain\Ticket\Repositories\TicketRepository;
use App\Domain\booking_Seat\Repositories\BookingSeatRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Domain\Booking\DTOs\BookingPayDTO;
use App\Domain\Booking\Models\Booking;
use App\Domain\Invoice\Services\InvoiceService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Domain\Booking\DTOs\BookingSummaryDTO;

class BookingService
{
    public function __construct(
        private readonly BookingRepository $bookingRepository,
        private readonly BookingSeatRepository $bookingSeatRepository,
        private readonly EventSeatRepository $eventSeatRepository,
        private readonly PaymentRepository $paymentRepository,
        private readonly TicketRepository $ticketRepository,
        private readonly InvoiceService $invoiceService,
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

    public function payBooking(BookingPayDTO $dto): array
    {
        //$paymentResult = $data['payment_result'] ?? 'exitoso';

        return DB::transaction(function () use ($dto) {
            $booking = $this->bookingRepository->findByBookingIdForUpdate($dto->bookingId());

            if (! $booking) {
                throw ValidationException::withMessages([
                    'booking_id' => ['Booking not found.'],
                ]);
            }

            if (in_array($booking->status, [
                \App\Domain\Booking\Models\Booking::STATUS_PAID,
                \App\Domain\Booking\Models\Booking::STATUS_CANCELLED,
                \App\Domain\Booking\Models\Booking::STATUS_EXPIRED,
                \App\Domain\Booking\Models\Booking::STATUS_PROCESSING_PAYMENT,
            ], true)) {
                throw ValidationException::withMessages([
                    'booking_id' => ['This booking cannot be paid in its current status.'],
                ]);
            }

            // validar si es pago en efectivo o con tarjeta
            if($dto->paymentMethod() === 'tarjeta')
            {

                // 1. hay que realizar una validacion para los datos de la tarjeta, que esten completos
                // 2. hay que cambiar el estado del booking a proceso_pago para evitar que se ejecute el proceso de expiracion mientras se procesa el pago
                
                // integracion de logica para pasarela de pagos
                //$result = PayBookingTarget(); // retorna true o false
                /* if(!$result)
                {
                    return $this->payBookingFailed($booking);
                }

                return $this->payBookingSuccess($booking, $dto); */

            }else
            {
                // lógica para pago en efectivo
                return $this->payBookingSuccess($booking, $dto);
            }
        });
    }

    public function payBookingSuccess(Booking $booking, BookingPayDTO $dto): array
    {
        $now = now();
        $transactionReference = (string) Str::uuid();

        $bookingSeats = $this->bookingSeatRepository->findByBookingIdWithEventSeat($booking->id);

        $payment = $this->paymentRepository->create([
            'booking_id' => $booking->id,
            'provider' => $dto->paymentMethod(),
            'provider_reference' => $transactionReference,
            'amount' => $booking->total,
            'status' => 'pagado',
            'paid_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $this->bookingRepository->markAsPaid($booking, [
            'payment_method' => $dto->paymentMethod(),
            'transaction_reference' => $transactionReference,
            'confirmed_at' => $now,
        ]);

        $this->bookingSeatRepository->updateStatusByBookingId($booking->id, 'confirmado');
        $this->eventSeatRepository->updateStatusByIds($bookingSeats->pluck('event_seat_id')->all(), 'vendido');

        $ticketRows = [];

        foreach ($bookingSeats as $bookingSeat) {
            $ticketRows[] = [
                'booking_id' => $booking->id,
                'booking_seat_id' => $bookingSeat->id,
                'event_id' => $booking->event_id,
                'user_id' => $booking->user_id,
                'ticket_type' => $dto->ticketType(),
                'qr_code' => (string) Str::uuid(),
                'status' => 'emitido',
                'issued_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        $this->ticketRepository->insertMany($ticketRows);

        // Creacion de Factura si el tipo de ticket es tarjeta o efectivo
        if($dto->ticketType() === 'tarjeta' || $dto->ticketType() === 'efectivo')
        {
            $this->invoiceService->createInvoiceForPayment($booking->id, $payment->id, $dto->nit() ?: 'C/F');
        }

        return [
            'success' => true,
            'message' => 'Payment completed successfully.',
            'booking_id' => $booking->id,
            'payment_id' => $payment->id,
            'tickets_count' => count($ticketRows),
        ];
    }

    public function payBookingFailed(Booking $booking) : array
    {
        $now = now();
        $transactionReference = (string) Str::uuid();

        $payment = $this->paymentRepository->create([
            'booking_id' => $booking->id,
            'provider' => 'Tarjeta',
            'provider_reference' => $transactionReference,
            'amount' => $booking->total,
            'status' => 'fallido',
            'paid_at' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $bookingSeats = $this->bookingSeatRepository->findByBookingIdWithEventSeat($booking->id);
        $eventSeatIds = $bookingSeats->pluck('event_seat_id')->all();

        $this->bookingSeatRepository->updateStatusByBookingId($booking->id, 'expirado');

        if (! empty($eventSeatIds)) {
            $this->eventSeatRepository->updateStatusByIds($eventSeatIds, 'disponible');
        }

        $this->bookingRepository->markAsCancelled($booking, [
            'payment_method' => 'Tarjeta',
            'transaction_reference' => $transactionReference,
            'cancelled_at' => $now,
        ]);

        return [
            'success' => false,
            'message' => 'Payment failed and seats were released.',
            'booking_id' => $booking->id,
            'payment_id' => $payment->id,
        ];
    }

    public function getBookingSummary(int $bookingId, int $userId): BookingSummaryDTO {

        $booking = $this->bookingRepository
            ->findSummaryByIdAndUser(
                $bookingId,
                $userId
            );

        if (!$booking) {
            throw new NotFoundHttpException(
                'Reserva no encontrada'
            );
        }

        // Verificar que la reserva tenga estado pagado
        if ($booking->status !== Booking::STATUS_PAID) {
            throw ValidationException::withMessages([
                'booking_id' => ['This booking cannot be paid in its current status.'],
            ]);
        }

        $tickets = [];

        foreach ($booking->tickets as $ticket) {

            $seat = $ticket
                ->bookingSeat
                ->eventSeat
                ->seat;

            $tickets[] = [
                'ticket_id' => $ticket->id,
                'qr_code' => $ticket->qr_code,
                'seat' => $seat->row_label . '-' . $seat->seat_number,
                'section' => $seat->section->name,
            ];
        }

        return new BookingSummaryDTO(
            booking: [
                'id' => $booking->id,
                'reference' => $booking->reference,
                'status' => $booking->status,
                'total' => $booking->total,
            ],

            event: [
                'id' => $booking->event->id,
                'title' => $booking->event->title,
                'date' => $booking->event->starts_at,
            ],

            customer: [
                'id' => $booking->user->id,
                'name' => $booking->user->name,
                'email' => $booking->user->email,
            ],

            invoice: $booking->invoice
                ? [
                    'invoice_number' => $booking->invoice->invoice_number,
                    'nit' => $booking->invoice->nit,
                    'subtotal' => $booking->invoice->subtotal,
                    'tax_amount' => $booking->invoice->tax_amount,
                    'total' => $booking->invoice->total,
                    'issued_at' => $booking->invoice->issued_at,
                ]
                : null,

            tickets: $tickets
        );
    }
}
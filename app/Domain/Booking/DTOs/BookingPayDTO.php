<?php

namespace App\Domain\Booking\DTOs;

class BookingPayDTO
{
    public function __construct(
        private readonly int $bookingId,
        private readonly string $ticketType,
        private readonly string $paymentMethod,
        private readonly string $numberTarget,
        private readonly string $monthTarget,
        private readonly string $yearTarget,
        private readonly string $cvcTarget,
        private readonly string $nameTarget,
        private readonly string $nit,
    ){}

    public static function fromArray(array $data): self
    {
        return new self(
            (int) $data['booking_id'],
            $data['ticket_type'] ?? 'tarjeta',
            $data['payment_method'] ?? '',
            $data['number_target'] ?? '',
            $data['month_target'] ?? '',
            $data['year_target'] ?? '',
            $data['cvc_target'] ?? '',
            $data['name_target'] ?? '',
            $data['nit'] ?? 'C/F',
        );
    }

    public function bookingId(): int
    {
        return $this->bookingId;
    }

    public function ticketType(): string
    {
        return $this->ticketType;
    }

    public function paymentMethod(): string
    {
        return $this->paymentMethod;
    }

    public function numberTarget(): string
    {
        return $this->numberTarget;
    }

    public function monthTarget(): string
    {
        return $this->monthTarget;
    }

    public function yearTarget(): string
    {
        return $this->yearTarget;
    }

    public function cvcTarget(): string
    {
        return $this->cvcTarget;
    }

    public function nameTarget(): string
    {
        return $this->nameTarget;
    }

    public function nit(): string
    {
        return $this->nit;
    }
}
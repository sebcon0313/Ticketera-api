<?php

namespace App\Domain\Booking\DTOs;

class BookingSummaryDTO
{
    public function __construct(
        public readonly array $booking,
        public readonly array $event,
        public readonly array $customer,
        public readonly ?array $invoice,
        public readonly array $tickets
    ) {}
}
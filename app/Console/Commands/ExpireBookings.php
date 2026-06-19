<?php

namespace App\Console\Commands;

use App\Domain\Booking\Services\BookingExpirationService;
use Illuminate\Console\Command;

class ExpireBookings extends Command
{
    protected $signature = 'bookings:expire';

    protected $description = 'Expire overdue bookings and release reserved seats';

    public function __construct(
        private readonly BookingExpirationService $service
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $expiredCount = $this->service->expireOverdueReservations();

        $this->info("Expired bookings: {$expiredCount}");

        return self::SUCCESS;
    }
}
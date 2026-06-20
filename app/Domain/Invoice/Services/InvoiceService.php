<?php

namespace App\Domain\Invoice\Services;

use App\Domain\Invoice\Repositories\InvoiceRepositoryInterface;
use App\Domain\Booking\Models\Booking;

class InvoiceService
{
    public function __construct(
        private readonly InvoiceRepositoryInterface $invoiceRepository
    ) {}

    public function createInvoiceForPayment(int $bookingId, int $paymentId, string $nit): void
    {
        $booking = Booking::find($bookingId);

        // caluculo del subtotal
        $iva = 12;
        $subtotal = $booking->total / (1 + ($iva / 100));

        // Calulo del monto de impuestos
        $taxAmount = $booking->total - $subtotal;

        $this->invoiceRepository->create([
            'booking_id' => $bookingId,
            'payment_id' => $paymentId,
            'invoice_number' => 'INV-' . str_pad($bookingId, 6, '0', STR_PAD_LEFT),
            'nit' => $nit ?? 'C/F',
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total' => $booking->total,
            'issued_at' => now(),
        ]);
    }
}
<?php

namespace App\Domain\Invoice\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Domain\Booking\Models\Booking;
use App\Domain\Payments\Models\Payment;

class Invoice extends Model
{
    protected $table = 'invoices';
    public const STATUS_ISSUED = 'emitida';
    public const STATUS_CANCELLED = 'anulada';

    protected $fillable = [
        'booking_id',
        'payment_id',
        'invoice_number',
        'nit',
        'subtotal',
        'tax_amount',
        'total',
        'status',
        'pdf_path',
        'issued_at',
        'cancelled_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'issued_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    /**
     * Reserva asociada.
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Pago asociado.
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * Verifica si la factura está anulada.
     */
    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /**
     * Verifica si la factura está emitida.
     */
    public function isIssued(): bool
    {
        return $this->status === self::STATUS_ISSUED;
    }
}
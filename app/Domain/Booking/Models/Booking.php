<?php

namespace App\Domain\Booking\Models;

use App\Domain\Event\Models\Event;
use App\Domain\booking_Seat\Models\BookingSeat;
use App\Domain\User\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Domain\Invoice\Models\Invoice;

class Booking extends Model
{
    public const STATUS_PENDING = 'pendiente';
    public const STATUS_RESERVED = 'reservado';
    public const STATUS_PROCESSING_PAYMENT = 'proceso_pago';
    public const STATUS_CONFIRMED = 'confirmado';
    public const STATUS_PAID = 'pagado';
    public const STATUS_CANCELLED = 'cancelado';
    public const STATUS_EXPIRED = 'expirado';

    protected $fillable = [
        'reference',
        'user_id',
        'event_id',
        'total',
        'payment_method',
        'transaction_reference',
        'status',
        'reserved_until',
        'confirmed_at',
        'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
            'reserved_until' => 'datetime',
            'confirmed_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function bookingSeats(): HasMany
    {
        return $this->hasMany(BookingSeat::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany('App\\Domain\\Payments\\Models\\Payment');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany('App\\Domain\\Ticket\\Models\\Ticket');
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }
}
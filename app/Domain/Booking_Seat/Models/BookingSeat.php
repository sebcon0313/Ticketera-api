<?php

namespace App\Domain\booking_Seat\Models;

use App\Domain\Booking\Models\Booking;
use App\Domain\Event_Seat\Models\EventSeat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingSeat extends Model
{
    protected $table = 'booking_seat';

    protected $fillable = [
        'booking_id',
        'event_seat_id',
        'price_snapshot',
        'hold_expires_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'price_snapshot' => 'decimal:2',
            'hold_expires_at' => 'datetime',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function eventSeat(): BelongsTo
    {
        return $this->belongsTo(EventSeat::class);
    }
}
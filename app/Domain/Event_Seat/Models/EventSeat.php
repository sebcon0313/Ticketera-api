<?php

namespace App\Domain\Event_Seat\Models;

use App\Domain\Event\Models\Event;
use App\Domain\Seat\Models\Seat;
use App\Domain\booking_Seat\Models\BookingSeat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventSeat extends Model
{
    protected $fillable = [
        'event_id',
        'seat_id',
        'status',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function seat(): BelongsTo
    {
        return $this->belongsTo(Seat::class);
    }

    public function bookingSeats(): HasMany
    {
        return $this->hasMany(BookingSeat::class);
    }
}
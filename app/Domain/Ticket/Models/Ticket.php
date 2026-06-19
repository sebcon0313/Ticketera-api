<?php

namespace App\Domain\Ticket\Models;

use App\Domain\Booking\Models\Booking;
use App\Domain\Event\Models\Event;
use App\Domain\Seat\Models\Seat;
use App\Domain\User\Models\User;
use App\Domain\booking_Seat\Models\BookingSeat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    protected $fillable = [
        'booking_id',
        'booking_seat_id',
        'event_id',
        'user_id',
        'ticket_type',
        'qr_code',
        'status',
        'issued_at',
        'used_at',
        'pdf_path',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'datetime',
            'used_at' => 'datetime',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function bookingSeat(): BelongsTo
    {
        return $this->belongsTo(BookingSeat::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function seat(): BelongsTo
    {
        return $this->belongsTo(Seat::class);
    }
}
<?php

namespace App\Domain\Booking\Models;

use App\Domain\Event\Models\Event;
use App\Domain\User\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
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
        return $this->hasMany('App\\Domain\\booking_Seat\\Models\\BookingSeat');
    }
}
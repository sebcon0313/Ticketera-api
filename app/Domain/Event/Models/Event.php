<?php

namespace App\Domain\Event\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Domain\Venue\Models\Venue;
use App\Domain\User\Models\User;

class Event extends Model
{
    protected $fillable = [
        'venue_id',
        'created_by',
        'title',
        'description',
        'starts_at',
        'ends_at',
        'image_url',
        'status',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'published_at' => 'datetime',
        ];
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /* public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    } */

}

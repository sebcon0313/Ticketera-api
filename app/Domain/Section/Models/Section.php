<?php

namespace App\Domain\Section\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Domain\Venue\Models\Venue;
use App\Domain\Seat\Models\Seat;

class Section extends Model
{
    protected $fillable = [
        'venue_id',
        'name',
        'code',
        'map_config',
    ];
    protected function casts(): array
    {
        return [
            'map_config' => 'array',
        ];
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function seats(): HasMany
    {
        return $this->hasMany(Seat::class);
    }
}

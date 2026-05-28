<?php

namespace App\Domain\Venue\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Domain\Event\Models\Event;

class Venue extends Model
{
    protected $fillable = [
        'name', 
        'address', 
        'city', 
        'country',  
        'seat_map_config'
    ];

    protected function casts(): array
    {
        return [
            'seat_map_config' => 'array',
        ];
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    /* public function sections(): HasMany
    {
        return $this->hasMany(Section::class);
    } */
}

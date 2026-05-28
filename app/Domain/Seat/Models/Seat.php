<?php

namespace App\Domain\Seat\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Domain\Section\Models\Section;

class Seat extends Model
{
    protected $fillable = [
        'section_id',
        'row_label',
        'seat_number',
        'base_price',
        'status',
    ];
    protected function casts(): array
    {
        return [
            'base_price' => 'decimal:2',
        ];
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }
}

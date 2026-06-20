<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingSummaryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'booking' => $this->booking,
            'event' => $this->event,
            'customer' => $this->customer,
            'invoice' => $this->invoice,
            'tickets' => $this->tickets,
        ];
    }
}
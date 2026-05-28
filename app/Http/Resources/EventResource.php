<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            // Datos principales
            'id'          => $this->id,
            'title'       => $this->title,
            'description' => $this->description,

            // Fechas
            'starts_at' => optional($this->starts_at)->format('Y-m-d H:i:s'),
            'ends_at'   => optional($this->ends_at)->format('Y-m-d H:i:s'),

            // Imagen
            'image_url' => $this->image_url,

            // Estado
            'status'       => $this->status,
            'published_at' => optional($this->published_at)->format('Y-m-d H:i:s'),

            // Venue relacionado
            'venue' => [
                'id'      => $this->venue?->id,
                'name'    => $this->venue?->name,
                'address' => $this->venue?->address,
                'city'    => $this->venue?->city,
                'country' => $this->venue?->country,
            ],

            // Usuario creador
            'created_by' => [
                'id'    => $this->organizer?->id,
                'name'  => $this->organizer?->name,
                'email' => $this->organizer?->email,
            ],

            // Datos calculados útiles para frontend Angular
            'is_active' => $this->status === 'publicado'
                && now()->lt($this->starts_at),

            'has_started' => now()->gte($this->starts_at),

            // Auditoría
            'created_at' => optional($this->created_at)->format('Y-m-d H:i:s'),
            'updated_at' => optional($this->updated_at)->format('Y-m-d H:i:s'),
        ];
    }
}

/* 
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'category' => $this->category,
            'image_url' => $this->image_url,
            'status' => $this->status,
            'starts_at' => optional($this->starts_at)?->toIso8601String(),
            'ends_at' => optional($this->ends_at)?->toIso8601String(),
            'published_at' => optional($this->published_at)?->toIso8601String(),
            'venue' => new VenueResource($this->whenLoaded('venue')),
            'organizer' => new UserResource($this->whenLoaded('organizer')),
        ];
    }
}
 */
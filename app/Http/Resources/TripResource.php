<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TripResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'available_seats' => $this->available_seats,
            'countries'       => CountryResource::collection($this->whenLoaded('countries')),
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VacationTypeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'Vacation Type' =>[
                'id' => $this->id,
                'name' => $this->name,
                'is_paid' => $this->is_paid,
                'max_days_per_year' => $this->max_days_per_year
            ]
        ];
    }
}

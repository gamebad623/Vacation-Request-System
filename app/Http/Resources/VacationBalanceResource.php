<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VacationBalanceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'year' => $this->year,
            'balance' => $this->balance,
            'used' => $this->used,
            'remaining' => $this->remaining,
            'user'=> new UserResource($this->whenLoaded('user')),
            'vacation_type' => new VacationTypeResource($this->whenLoaded('vacationType'))

        ];
    }
}

<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VacationRequestResource extends JsonResource
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
            'vacation_type_id' => $this->vacation_type_id,
            'Duration' => [
                'start_date' => $this->start_date ? Carbon::parse($this->start_date)->format('Y-m-d'): null,
                'end_date' => $this->end_date ? Carbon::parse($this->end_date )->format('Y-m-d'): null,
                'total_days' => $this->total_days
            ],
            'reason' => $this->reason,
            'status' => $this->status,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}

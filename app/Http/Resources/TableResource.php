<?php

namespace App\Http\Resources;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TableResource extends JsonResource
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
            'table_name' => $this->table_name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'tickets' => $this->relationLoaded('tickets') && $this->tickets->isNotEmpty() ? Ticket::collection($this->tickets) : null,
        ];
    }
}

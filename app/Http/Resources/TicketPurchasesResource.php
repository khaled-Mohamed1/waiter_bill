<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketPurchasesResource extends JsonResource
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
            'product_id' => $this->product_id,
            'ticket_id' => $this->ticket_id,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'discount' => $this->discount,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

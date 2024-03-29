<?php

namespace App\Http\Resources;

use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
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
            'ticket_name' => $this->ticket_name ?? null,
            'ticket_total' => $this->ticket_total,
            'ticket_type' => $this->ticket_type,
            'ticket_total_discount' => $this->ticket_total_discount,
            'ticket_total_summation' => $this->ticket_total_summation,
            'ticket_paid' => $this->ticket_paid,
            'ticket_rest' => $this->ticket_rest,
            'ticket_payment' => $this->ticket_payment,
            'ticket_status' => $this->ticket_status,
            'ticket_note' => $this->ticket_note,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'table_id' => TableResource::make(Table::find($this->table_id)),
            'purchases' => $this->relationLoaded('ticketPurchases') && $this->ticketPurchases->isNotEmpty() ? TicketPurchasesResource::collection($this->ticketPurchases) : null,
        ];
    }
}

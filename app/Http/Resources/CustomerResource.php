<?php

namespace App\Http\Resources;

use App\Http\Resources\OneReceiptResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
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
            'customer_name' => $this->customer_name,
            'customer_email' => $this->customer_email,
            'customer_phone_number' => $this->customer_phone_number,
            'customer_image' => $this->customer_image,
            'customer_notes' => $this->customer_notes,
            'customer_address' => $this->customer_address,
            'user_id' => $this->user_id,
            'company_id' => $this->company_id,
            'wallet' => $this->wallet,
            'customer_status' => $this->customer_status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'receipts' => $this->relationLoaded('receipts') && $this->receipts->isNotEmpty() ? OneReceiptResource::collection($this->receipts) : null,
        ];
    }
}

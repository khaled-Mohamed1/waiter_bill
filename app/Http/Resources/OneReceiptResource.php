<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OneReceiptResource extends JsonResource
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
            'receipt_number' => $this->receipt_number,
            'total' => $this->total,
            'total_discount' => $this->total_discount,
            'total_summation' => $this->total_summation,
            'status' => $this->status,
            'status_bill' => $this->status_bill,
            'status_pay' => $this->status_pay,
            'refunded_value' => $this->refunded_value,
            'amount_paid' => $this->amount_paid,
            'rest'  => $this->rest,
            'company_id' => $this->company_id,
            'customer_id' => $this->customer_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user_id' => OneUserResource::make(User::find($this->user_id)),
            'purchases' => $this->relationLoaded('purchases') && $this->purchases->isNotEmpty() ? PurchaseResource::collection($this->purchases) : null,
        ];
    }
}

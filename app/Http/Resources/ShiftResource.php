<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShiftResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $expected_cash_amount = ($this->beginning_cash + $this->payments_cash + $this->payments) -
            ($this->refunds_cash + $this->withdrawal_amounts);

        $net_sales = $this->total_sales - ($this->discounts + $this->refunds_cash);

        return [
            'id' => $this->id,
            'shift_date' => $this->shift_date,
            'shift_time_start' => $this->shift_time_start,
            'shift_time_end' => $this->shift_time_end,
            'beginning_cash' => $this->beginning_cash,
            'payments_cash' => $this->payments_cash,
            'refunds_cash' => $this->refunds_cash,
            'payments' => $this->payments,
            'withdrawal_amounts' => $this->withdrawal_amounts,
            'expected_cash_amount' => $expected_cash_amount,
            'total_sales' => $this->total_sales,
            'discounts' => $this->discounts,
            'net_sales' => $net_sales,
            'cash_money' => $this->cash_money,
            'card' => $this->card,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'cashes' => CashManagementResource::collection($this->cashes),
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

    public static $wrap = 'product';

//    public function toArray(Request $request): array
//    {
//        return [
//            'product_name' => $this->product_name,
//            'product_color'=> $this->product_color,
//            'product_image' => $this->product_image,
//            'product_price' => $this->product_price,
//            'product_cost' => $this->product_cost,
//            'category_id' => $this->category_id,
//            'company_id' => $this->company_id,
//            'user_id' => $this->user_id,
//            'sku' => $this->sku,
//            'bar_code' => $this->bar_code,
//            'sold_by' => $this->sold_by,
//            'expiration_date' => $this->expiration_date,
//            'product_location' => $this->product_location,
//            'stock' => $this->stock,
//            'created_at' => $this->created_at,
//            'updated_at' => $this->updated_at,
//        ];
//    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
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
            'category_name' => $this->category_name,
            'category_color' => $this->category_color,
            'category_image' => $this->category_image,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'products' => $this->relationLoaded('products') && $this->products->isNotEmpty() ? ProductResource::collection($this->products) : null,
        ];
    }
}

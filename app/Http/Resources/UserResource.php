<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'username' => $this->username,
            'email' => $this->email,
            'mobile_number' => $this->mobile_number,
            'pin_code' => $this->pin_code,
            'address' => $this->address,
            'company_id' => $this->company_id,
            'wallet' => $this->wallet,
            'token' => $this->createToken("Token")->plainTextToken,
            'role_id' => $this->role_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'roles' => new RoleResource($this->roles[0]),
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleAccessControlResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'role_id' => $this->id,
            'permissions_id' => $this->permissions->pluck('id')->toArray(),
            'campaigns_id' => $this->campaigns->pluck('id')->toArray(),
        ];
    }
}

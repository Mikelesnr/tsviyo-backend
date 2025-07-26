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
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'driver' => $this->driverProfile ? [
                'is_activated' => $this->driverProfile->is_activated,
                'is_online' => $this->driverProfile->is_online,
                'is_suspended' => $this->driverProfile->is_suspended,
                'license_number' => $this->driverProfile->license_number,
            ] : null,
        ];
    }
}

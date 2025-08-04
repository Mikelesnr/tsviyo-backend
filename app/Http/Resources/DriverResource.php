<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DriverResource extends JsonResource
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

            // Pulling details from the associated User
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ],

            // Native Driver attributes
            'license_number' => $this->license_number ?? null,
            'contact_number' => $this->contact_number ?? null,

            // Vehicle data
            'vehicle' => $this->vehicle
                ?? optional($this->vehicles->first(), function ($vehicle) {
                    return [
                        'id' => $vehicle->id,
                        'make' => $vehicle->make,
                        'model' => $vehicle->model,
                        'plate_number' => $vehicle->plate_number,
                        'image_url' => optional($vehicle->vehicleImage)->url,
                    ];
                }),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

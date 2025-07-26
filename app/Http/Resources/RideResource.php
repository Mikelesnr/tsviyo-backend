<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RideResource extends JsonResource
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
            'pickup_lat' => $this->pickup_lat,
            'pickup_lng' => $this->pickup_lng,
            'dropoff_lat' => $this->dropoff_lat,
            'dropoff_lng' => $this->dropoff_lng,
            'status' => $this->status->value,
            'has_paid' => $this->has_paid,
            'driver_id' => $this->driver_id,
            'rider_id' => $this->rider_id,
            'payment' => new PaymentResource($this->whenLoaded('payment')),
            'ratings' => RatingResource::collection($this->whenLoaded('ratings')),
        ];
    }
}

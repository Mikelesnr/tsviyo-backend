<?php

namespace App\Events;

use App\Models\Ride;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class RideRequested implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $ride;

    public function __construct(Ride $ride)
    {
        $this->ride = $ride;
    }

    // Broadcast on a public channel drivers are subscribed to
    public function broadcastOn(): Channel
    {
        return new Channel('rides.nearby');
    }

    // Customize the payload sent to clients
    public function broadcastWith(): array
    {
        return [
            'id' => $this->ride->id,
            'pickup_lat' => $this->ride->pickup_lat,
            'pickup_lng' => $this->ride->pickup_lng,
            'dropoff_lat' => $this->ride->dropoff_lat,
            'dropoff_lng' => $this->ride->dropoff_lng,
            'status' => $this->ride->status,
        ];
    }

    // Optional: rename event on frontend
    public function broadcastAs(): string
    {
        return 'RideRequested';
    }
}

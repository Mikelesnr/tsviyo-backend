<?php

namespace App\Events;

// use App\Models\Ride;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class RideRequested implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $ride;

    public function __construct($ride)
    {
        $this->ride = $ride;
    }

    // Broadcast on a public channel drivers are subscribed to
    public function broadcastOn(): Channel
    {
        return new Channel('rides.nearby');
    }


    // Optional: rename event on frontend
    public function broadcastAs(): string
    {
        return 'RideRequested';
    }
}

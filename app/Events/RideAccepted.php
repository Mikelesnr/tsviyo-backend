<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class RideAccepted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $ride;

    public function __construct($ride)
    {
        $this->ride = $ride;
    }

    public function broadcastOn()
    {
        return new PrivateChannel("ride.updates.{$this->ride->rider_id}");
        
    }

    public function broadcastAs()
    {
        return 'RideAccepted';
    }

    public function broadcastWith()
    {
        return [
            'ride' => [
                'id' => $this->ride->id,
                'rider_id' => $this->ride->rider_id,
                'status' => $this->ride->status,
                'driver_id' => $this->ride->driver_id,
                'pickup_add' => $this->ride->pickup_add,
                'dropoff_add' => $this->ride->dropoff_add,
                'pickup_lat' => $this->ride->pickup_lat,
                'pickup_lng' => $this->ride->pickup_lng,
                'dropoff_lat' => $this->ride->dropoff_lat,
                'dropoff_lng' => $this->ride->dropoff_lng,
                'fare' => $this->ride->fare,
                'pickup_time' => $this->ride->pickup_time,
            ]
        ];
    }
}
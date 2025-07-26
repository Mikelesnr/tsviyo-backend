<?php

namespace App\Http\Controllers;

use App\Enums\RideStatus;
use App\Events\RideRequested;
use App\Http\Controllers\Controller;
use App\Http\Resources\RideResource;
use App\Models\Ride;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RideController extends Controller
{
    /**
     * List rides requested by the authenticated rider.
     *
     * @group Ride
     * @authenticated
     * @header Authorization string required Bearer token used to authenticate the request. Example: "Bearer your-token"
     * @response 200 [{"id":1,"pickup_lat":-17.8252,"pickup_lng":31.0335,"dropoff_lat":-17.8291,"dropoff_lng":31.0405}]
     */
    public function index()
    {
        $rides = Ride::where('rider_id', Auth::id())->latest()->get();
        return RideResource::collection($rides);
    }

    /**
     * Request a new ride.
     *
     * After a successful request, the backend emits a `RideRequested` socket event
     * to the `rides.nearby` channel. Frontend drivers subscribed to this channel
     * can receive the ride details in real time.
     *
     * @group Ride
     * @authenticated
     * @header Authorization string required Bearer token used to authenticate the request. Example: "Bearer your-token"
     * @bodyParam pickup_lat float required Latitude of pickup point. Example: -17.8252
     * @bodyParam pickup_lng float required Longitude of pickup point. Example: 31.0335
     * @bodyParam dropoff_lat float required Latitude of dropoff point. Example: -17.8291
     * @bodyParam dropoff_lng float required Longitude of dropoff point. Example: 31.0405
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *     "status": "requested"
     *   }
     * }
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pickup_lat' => 'required|numeric',
            'pickup_lng' => 'required|numeric',
            'dropoff_lat' => 'required|numeric',
            'dropoff_lng' => 'required|numeric',
        ]);

        $ride = Ride::create([
            'rider_id' => Auth::id(),
            'driver_id' => '', // Unassigned at creation
            'pickup_lat' => $validated['pickup_lat'],
            'pickup_lng' => $validated['pickup_lng'],
            'dropoff_lat' => $validated['dropoff_lat'],
            'dropoff_lng' => $validated['dropoff_lng'],
            'status' => RideStatus::REQUESTED,
            'has_paid' => false,
        ]);

        broadcast(new RideRequested($ride))->toOthers();

        return new RideResource($ride);
    }

    /**
     * Cancel a ride before it's accepted by a driver.
     *
     * @group Ride
     * @authenticated
     * @header Authorization string required Bearer token used to authenticate the request. Example: "Bearer your-token"
     * @response 200 {"data":{"id":1,"status":"canceled"}}
     * @response 403 {"message":"Unauthorized or invalid cancellation stage"}
     */
    public function cancel(Ride $ride)
    {
        if ($ride->rider_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($ride->status !== RideStatus::REQUESTED) {
            return response()->json(['message' => 'Ride cannot be cancelled at this stage'], 403);
        }

        $ride->update(['status' => RideStatus::CANCELED]);

        return new RideResource($ride);
    }

    /**
     * Cancel a ride after it has been accepted — with reason.
     *
     * @group Ride
     * @authenticated
     * @header Authorization string required Bearer token used to authenticate the request. Example: "Bearer your-token"
     * @bodyParam reason string required Explanation for late cancellation. Example: "Emergency came up"
     * @response 200 {"message":"Ride cancelled after acceptance","data":{"id":1,"status":"canceled"}}
     * @response 403 {"message":"Unauthorized or invalid cancellation stage"}
     */
    public function lateCancel(Request $request, Ride $ride)
    {
        if ($ride->rider_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($ride->status !== RideStatus::ACCEPTED) {
            return response()->json(['message' => 'Ride not in a cancellable late stage'], 403);
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        $ride->update(['status' => RideStatus::CANCELED]);

        return response()->json([
            'message' => 'Ride cancelled after acceptance',
            'reason' => $validated['reason'],
            'data' => new RideResource($ride),
        ]);
    }
}

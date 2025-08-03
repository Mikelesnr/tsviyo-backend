<?php

namespace App\Http\Controllers;

use App\Enums\RideStatus;
use App\Events\RideRequested;
use App\Http\Controllers\Controller;
use App\Http\Resources\RideResource;
use App\Models\Ride;
use App\Models\Rider;
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
     * Creates a new ride record for the authenticated rider. The ride starts in a `requested` state
     * and may include pickup/dropoff coordinates, addresses, a fare estimate, and an optional pickup time.
     *
     * After a successful request, the backend emits a `RideRequested` socket event
     * to the `rides.nearby` channel. Frontend drivers subscribed to this channel
     * receive the ride details in real time.
     *
     * @group Ride
     * @authenticated
     *
     * @header Authorization string required Bearer token used to authenticate the request. Example: "Bearer your-token"
     *
     * @bodyParam pickup_lat float required Latitude of the pickup location. Example: -17.7978
     * @bodyParam pickup_lng float required Longitude of the pickup location. Example: 31.1259
     * @bodyParam dropoff_lat float required Latitude of the dropoff location. Example: -17.8147
     * @bodyParam dropoff_lng float required Longitude of the dropoff location. Example: 31.1447
     * @bodyParam pickup_address string optional Human-readable pickup address. Example: "Food Lovers Market Greendale, Harare"
     * @bodyParam dropoff_address string optional Human-readable dropoff address. Example: "Pick n Pay Kamfinsa, Harare"
     * @bodyParam pickup_time datetime optional Scheduled pickup time (YYYY-MM-DD HH:MM:SS). Example: "2025-07-29 08:15:00"
     * @bodyParam fare float optional Estimated fare in USD. Example: 6.50
     *
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *     "status": "requested",
     *     "pickup_lat": -17.7978,
     *     "pickup_lng": 31.1259,
     *     "dropoff_lat": -17.8147,
     *     "dropoff_lng": 31.1447,
     *     "pickup_add": "Food Lovers Market Greendale, Harare",
     *     "dropoff_add": "Pick n Pay Kamfinsa, Harare",
     *     "pickup_time": "2025-07-29 08:15:00",
     *     "fare": 6.50
     *   }
     * }
     */
    public function store(Request $request)
    {
        // Validate incoming request
        $validated = $request->validate([
            'pickup_lat' => 'required|numeric',
            'pickup_lng' => 'required|numeric',
            'dropoff_lat' => 'required|numeric',
            'dropoff_lng' => 'required|numeric',
            'pickup_address' => 'nullable|string',
            'dropoff_address' => 'nullable|string',
            'pickup_time' => 'nullable|date',
            'fare' => 'nullable|numeric',
        ]);

        // Resolve rider via authenticated user
        $rider = Rider::where('user_id', Auth::id())->first();

        if (!$rider) {
            return response()->json([
                'message' => 'No rider record found for the authenticated user.',
            ], 404);
        }

        // Create ride entry
        $ride = Ride::create([
            'rider_id' => $rider->id,
            'pickup_lat' => $validated['pickup_lat'],
            'pickup_lng' => $validated['pickup_lng'],
            'dropoff_lat' => $validated['dropoff_lat'],
            'dropoff_lng' => $validated['dropoff_lng'],
            'pickup_add' => $validated['pickup_address'] ?? null,
            'dropoff_add' => $validated['dropoff_address'] ?? null,
            'pickup_time' => $validated['pickup_time'] ?? null,
            'fare' => $validated['fare'] ?? null,
            'status' => 'requested',
        ]);

        // Broadcast ride request (optional)
        broadcast(new RideRequested($ride));


        // Return response
        return response()->json([
            'data' => $ride,
        ], 201);
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
        $rider = Rider::where('user_id', Auth::id())->first();

        if (!$rider || $ride->rider_id !== $rider->id) {
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
        $rider = Rider::where('user_id', Auth::id())->first();

        if (!$rider || $ride->rider_id !== $rider->id) {
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

<?php

namespace App\Http\Controllers;

use App\Models\Ride;
use App\Models\Driver;
use App\Http\Resources\RideResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Enums\RideStatus;

class DriverController extends Controller
{
    /**
     * List rides assigned to the authenticated driver.
     *
     * @group Driver Ride
     * @authenticated
     * @header Authorization string required Bearer token used to authenticate the request. Example: "Bearer your-token"
     */
    public function myRides(Request $request)
    {
        $driver = Driver::where('user_id', Auth::id())->first();

        if (!$driver) {
            return response()->json(['message' => 'Authenticated user is not a registered driver'], 403);
        }

        $rides = Ride::where('driver_id', $driver->id)
            ->latest()
            ->get();

        return RideResource::collection($rides);
    }

    /**
     * Accept a ride if it's not already taken.
     *
     * @group Driver Ride
     * @authenticated
     * @header Authorization string required Bearer token used to authenticate the request. Example: "Bearer your-token"
     * @response 200 {"data": {"id": 1, "status": "accepted"}}
     * @response 409 {"message": "Ride already assigned to a driver"}
     */
    public function acceptRide(Ride $ride)
    {
        if (!empty($ride->driver_id)) {
            return response()->json(['message' => 'Ride already assigned to a driver'], 409);
        }

        $driver = Driver::where('user_id', Auth::id())->first();

        if (!$driver) {
            return response()->json(['message' => 'Authenticated user is not a registered driver'], 403);
        }

        $ride->update([
            'driver_id' => $driver->id,
            'status' => RideStatus::ACCEPTED,
        ]);

        return new RideResource($ride);
    }

    /**
     * Cancel a ride after driver has accepted it.
     *
     * This allows the driver to cancel their assigned ride if necessary,
     * such as for vehicle breakdowns or unresponsive passengers.
     *
     * @group Driver Ride
     * @authenticated
     * @header Authorization string required Bearer token used to authenticate the request. Example: "Bearer your-token"
     * @urlParam ride int required The ID of the ride to cancel. Example: 7
     * @bodyParam reason string required Explanation for cancellation. Example: "Car broke down"
     * @response 200 {
     *   "message": "Ride cancelled by driver",
     *   "reason": "Car broke down",
     *   "data": {
     *     "id": 7,
     *     "status": "canceled"
     *   }
     * }
     * @response 403 {"message": "Unauthorized"}
     * @response 403 {"message": "Cannot cancel ride at this stage"}
     */
    public function cancelRide(Ride $ride, Request $request)
    {
        $driver = Driver::where('user_id', Auth::id())->first();

        if (!$driver || $ride->driver_id !== $driver->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (!in_array($ride->status, [RideStatus::ACCEPTED, RideStatus::IN_PROGRESS])) {
            return response()->json(['message' => 'Cannot cancel ride at this stage'], 403);
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        $ride->update(['status' => RideStatus::CANCELED]);

        return response()->json([
            'message' => 'Ride cancelled by driver',
            'reason' => $validated['reason'],
            'data' => new RideResource($ride),
        ]);
    }


    /**
     * Toggle the driver's online status.
     *
     * Drivers must be activated and not suspended to perform this action.
     *
     * @group Driver
     * @authenticated
     * @header Authorization string required Bearer token used to authenticate the request. Example: "Bearer your-token"
     * @response 200 {
     *   "message": "Driver status updated",
     *   "is_online": true
     * }
     * @response 403 {"message": "Unauthorized or suspended driver"}
     */
    public function toggleOnlineStatus(Request $request)
    {
        $driver = Driver::where('user_id', Auth::id())->first();

        if (! $driver || ! $driver->is_activated || $driver->is_suspended) {
            return response()->json([
                'message' => 'Unauthorized or suspended driver',
            ], 403);
        }

        $driver->update([
            'is_online' => ! $driver->is_online,
        ]);

        return response()->json([
            'message' => 'Driver status updated',
            'is_online' => $driver->is_online,
        ]);
    }
}

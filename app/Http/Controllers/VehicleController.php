<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\VehicleResource;

class VehicleController extends Controller
{
    /**
     * List paginated vehicles for the authenticated driver.
     *
     * @group Vehicle
     * @authenticated
     * @header Authorization string required Bearer token used to authenticate the request. Example: "Bearer your-token"
     * @queryParam page int The page number for pagination. Example: 1
     * @response 200 {
     *   "data": [
     *     { "id": 1, "make": "Toyota", "model": "Vitz", "plate_number": "XYZ123" }
     *   ],
     *   "meta": {
     *     "current_page": 1,
     *     "last_page": 2,
     *     "total": 15
     *   }
     * }
     */
    public function index()
    {
        $driver = Driver::where('user_id', Auth::id())->firstOrFail();
        $vehicles = $driver->vehicles()->paginate(10);

        return VehicleResource::collection($vehicles);
    }

    /**
     * Show a specific vehicle's details.
     *
     * @group Vehicle
     * @authenticated
     * @header Authorization string required Bearer token used to authenticate the request. Example: "Bearer your-token"
     * @urlParam vehicle int required The ID of the vehicle.
     * @response 200 {
     *   "data": { "id": 1, "make": "Toyota", "model": "Vitz", "plate_number": "XYZ123" }
     * }
     */
    public function show(Vehicle $vehicle)
    {
        $driver = Driver::where('user_id', Auth::id())->first();

        if (!$driver || $vehicle->driver_id !== $driver->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return new VehicleResource($vehicle->load('driver'));
    }

    /**
     * Create a new vehicle for the authenticated driver.
     *
     * @group Vehicle
     * @authenticated
     * @header Authorization string required Bearer token used to authenticate the request. Example: "Bearer your-token"
     * @bodyParam make string required The vehicle's make. Example: "Toyota"
     * @bodyParam model string required The vehicle's model. Example: "Vitz"
     * @bodyParam plate_number string required The license plate number. Example: "XYZ123"
     * @response 201 {
     *   "message": "Vehicle created",
     *   "vehicle": { "id": 1, "make": "Toyota", "model": "Vitz", "plate_number": "XYZ123" }
     * }
     */
    public function store(Request $request)
    {
        $request->validate([
            'make' => 'required|string',
            'model' => 'required|string',
            'plate_number' => 'required|string|unique:vehicles,plate_number',
        ]);

        $driver = Driver::where('user_id', Auth::id())->firstOrFail();

        $vehicle = $driver->vehicles()->create($request->only('make', 'model', 'plate_number'));

        return response()->json([
            'message' => 'Vehicle created',
            'vehicle' => $vehicle,
        ], 201);
    }

    /**
     * Update a specific vehicle.
     *
     * @group Vehicle
     * @authenticated
     * @header Authorization string required Bearer token used to authenticate the request. Example: "Bearer your-token"
     * @urlParam id int required The ID of the vehicle.
     * @bodyParam make string The vehicle's make. Example: "Nissan"
     * @bodyParam model string The vehicle's model. Example: "Sunny"
     * @bodyParam plate_number string The license plate number. Example: "XYZ999"
     * @response 200 {
     *   "message": "Vehicle updated",
     *   "vehicle": { "id": 1, "make": "Nissan", "model": "Sunny", "plate_number": "XYZ999" }
     * }
     */
    public function update(Request $request, $id)
    {
        $vehicle = Vehicle::where('driver_id', Auth::id())->findOrFail($id);

        $vehicle->update($request->only('make', 'model', 'plate_number'));

        return response()->json([
            'message' => 'Vehicle updated',
            'vehicle' => $vehicle,
        ]);
    }

    /**
     * Delete a vehicle and deactivate driver if no vehicles remain.
     *
     * @group Vehicle
     * @authenticated
     * @header Authorization string required Bearer token used to authenticate the request. Example: "Bearer your-token"
     * @urlParam id int required The ID of the vehicle.
     * @response 200 {
     *   "message": "Vehicle deleted and driver deactivated"
     * }
     */
    public function destroy($id)
    {
        $driver = Driver::where('user_id', Auth::id())->firstOrFail();

        $vehicle = $driver->vehicles()->findOrFail($id);
        $vehicle->delete();

        if ($driver->vehicles()->count() === 0) {
            $driver->update(['is_activated' => false]);
        }

        return response()->json([
            'message' => 'Vehicle deleted' . ($driver->is_activated ? '' : ' and driver deactivated'),
        ]);
    }
}

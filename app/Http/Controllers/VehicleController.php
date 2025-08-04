<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\Image;
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
     * This method creates a vehicle tied to the driver ID (not the user ID).
     * If an image URL is provided, it attaches the image with type restricted to 'vehicle'.
     *
     * @group Vehicle
     * @authenticated
     * @header Authorization string required Bearer token used to authenticate the request. Example: "Bearer your-token"
     * @bodyParam make string required The vehicle's make. Example: "Toyota"
     * @bodyParam model string required The vehicle's model. Example: "Vitz"
     * @bodyParam plate_number string required The license plate number. Example: "XYZ123"
     * @bodyParam image_url string optional URL of the vehicle's image. Example: "https://imgur.com/car.png"
     * @response 201 {
     *   "message": "Vehicle created",
     *   "vehicle": {
     *     "id": 1,
     *     "make": "Toyota",
     *     "model": "Vitz",
     *     "plate_number": "XYZ123",
     *     "image": { "url": "https://imgur.com/car.png", "type": "vehicle" }
     *   }
     * }
     * @response 422 {"message": "The image type is invalid"}
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'make' => 'required|string',
            'model' => 'required|string',
            'plate_number' => 'required|string|unique:vehicles,plate_number',
            'image_url' => 'nullable|url',
        ]);

        $driver = Driver::where('user_id', Auth::id())->firstOrFail();

        // Check if driver already has a vehicle
        if ($driver->vehicles()->exists()) {
            return response()->json([
                'message' => 'Driver already has a registered vehicle.',
            ], 409);
        }

        $vehicle = $driver->vehicles()->create([
            'make' => $validated['make'],
            'model' => $validated['model'],
            'plate_number' => $validated['plate_number'],
        ]);

        if (!empty($validated['image_url'])) {
            // Only create image if one doesn't already exist
            if (!$vehicle->vehicleImage()->exists()) {
                $vehicle->vehicleImage()->create([
                    'url' => $validated['image_url'],
                    'type' => 'vehicle',
                    'vehicle_id' => $vehicle->id,
                ]);
            }
        }

        return response()->json([
            'message' => 'Vehicle created',
            'vehicle' => [
                'id' => $vehicle->id,
                'make' => $vehicle->make,
                'model' => $vehicle->model,
                'plate_number' => $vehicle->plate_number,
                'image' => $vehicle->vehicleImage
                    ? [
                        'id' => $vehicle->vehicleImage->id,
                        'vehicle_id' => $vehicle->vehicleImage->vehicle_id,
                        'url' => $vehicle->vehicleImage->url,
                        'type' => $vehicle->vehicleImage->type,
                    ]
                    : null,
            ],
        ], 201);
    }

    /**
     * Update a specific vehicle and its image.
     *
     * This endpoint updates a vehicle's details and, if a new image URL is provided,
     * updates the vehicle's image (matched via driver_id). Only the image's URL is editable.
     *
     * @group Vehicle
     * @authenticated
     * @header Authorization string required Bearer token used to authenticate the request. Example: "Bearer your-token"
     * @urlParam id int required The ID of the vehicle.
     * @bodyParam make string The vehicle's make. Example: "Nissan"
     * @bodyParam model string The vehicle's model. Example: "Sunny"
     * @bodyParam plate_number string The license plate number. Example: "XYZ999"
     * @bodyParam image_url string optional Updated image URL for the vehicle. Example: "https://imgur.com/car_updated.png"
     * @response 200 {
     *   "message": "Vehicle updated",
     *   "vehicle": {
     *     "id": 1,
     *     "make": "Nissan",
     *     "model": "Sunny",
     *     "plate_number": "XYZ999",
     *     "image": {
     *       "id": 12,
     *       "vehicle_id": 1,
     *       "url": "https://imgur.com/car_updated.png",
     *       "type": "vehicle"
     *     }
     *   }
     * }
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'make' => 'sometimes|string',
            'model' => 'sometimes|string',
            'plate_number' => 'sometimes|string|unique:vehicles,plate_number,' . $id,
            'image_url' => 'nullable|url',
        ]);

        $vehicle = Vehicle::where('driver_id', Auth::id())->findOrFail($id);

        $vehicle->update(array_filter($validated, fn($k) => in_array($k, ['make', 'model', 'plate_number']), ARRAY_FILTER_USE_KEY));

        // Update image URL if provided
        if (!empty($validated['image_url'])) {
            $image = Image::where('vehicle_id', $vehicle->id)->where('type', 'vehicle')->first();

            if ($image) {
                $image->update(['url' => $validated['image_url']]);
            }
        }

        return response()->json([
            'message' => 'Vehicle updated',
            'vehicle' => [
                'id' => $vehicle->id,
                'make' => $vehicle->make,
                'model' => $vehicle->model,
                'plate_number' => $vehicle->plate_number,
                'image' => $vehicle->vehicleImage
                    ? [
                        'id' => $vehicle->vehicleImage->id,
                        'vehicle_id' => $vehicle->vehicleImage->vehicle_id,
                        'url' => $vehicle->vehicleImage->url,
                        'type' => $vehicle->vehicleImage->type,
                    ]
                    : null,
            ],
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

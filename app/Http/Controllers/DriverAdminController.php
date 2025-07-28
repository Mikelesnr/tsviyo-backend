<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use App\Enums\UserRole;
use App\Models\Image;

/**
 * @group Admin - Driver Management
 * Endpoints for managing driver profiles. Accessible to admins only.
 */
class DriverAdminController extends Controller
{
    /**
     * Get the authenticated user's driver profile and profile image.
     *
     * Only accessible to users with the 'driver' role.
     *
     * @authenticated
     * @header Authorization string required Bearer token used to authenticate the request. Example: "Bearer your-token"
     *
     * @response 200 {
     *   "driver": {
     *     "id": 1,
     *     "user_id": 25,
     *     "license_number": "DLX-0071",
     *     "is_activated": false,
     *     "is_online": false,
     *     "is_suspended": false
     *   },
     *   "image": {
     *     "url": "https://example.com/images/avatar.jpg",
     *     "type": "profile"
     *   }
     * }
     * @response 403 {
     *   "message": "Only drivers may access driver profiles."
     * }
     * @response 404 {
     *   "message": "Driver profile not found."
     * }
     */
    public function showProfile()
    {
        $user = Auth::user();

        if ($user->role !== UserRole::Driver) {
            return response()->json(['message' => 'Only drivers may access driver profiles.'], 403);
        }

        $driver = $user->driver;
        if (!$driver) {
            return response()->json(['message' => 'Driver profile not found.'], 404);
        }

        $image = $user->profileImage;

        return response()->json([
            'driver' => $driver,
            'image' => $image ? [
                'url' => $image->url,
                'type' => $image->type,
            ] : null,
        ]);
    }

    /**
     * Create a driver profile for the authenticated user.
     *
     * Only accessible to users with the 'driver' role.
     *
     * @authenticated
     * @header Authorization string required Bearer token used to authenticate the request. Example: "Bearer your-token"
     *
     * @bodyParam license_number string required The driver’s license number. Example: "DLX-0071"
     * @bodyParam image_url string The URL to the driver's profile image. Must be a valid URL. Example: "https://example.com/images/avatar.jpg"
     *
     * @response 201 {
     *   "message": "Driver profile created",
     *   "driver": {
     *     "id": 1,
     *     "user_id": 25,
     *     "license_number": "DLX-0071",
     *     "is_activated": false,
     *     "is_online": false,
     *     "is_suspended": false
     *   }
     * }
     * @response 403 {
     *   "message": "Only drivers may create driver profiles."
     * }
     * @response 409 {
     *   "message": "Driver profile already exists."
     * }
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== UserRole::Driver) {
            return response()->json(['message' => 'Only drivers may create driver profiles.'], 403);
        }

        if ($user->driver) {
            return response()->json(['message' => 'Driver profile already exists.'], 409);
        }

        $validated = $request->validate([
            'license_number' => 'required|string',
            'image_url' => 'nullable|url',
        ]);

        $driver = Driver::create([
            'user_id' => $user->id,
            'license_number' => $validated['license_number'],
        ]);

        // Optionally create a profile image
        if (!empty($validated['image_url'])) {
            Image::create([
                'url' => $validated['image_url'],
                'type' => 'profile',
                'user_id' => $user->id,
                'vehicle_id' => null,
            ]);
        }

        return response()->json([
            'message' => 'Driver profile created',
            'driver' => $driver,
        ], 201);
    }

    /**
     * Update a driver’s profile.
     *
     * Admins can modify license number, activation, suspension, and online status.
     *
     * @authenticated
     * @header Authorization string required Bearer token used to authenticate the request. Example: "Bearer your-token"
     * @urlParam id int required The ID of the driver profile. Example: 1
     *
     * @bodyParam license_number string The new license number. Example: "DLX-0099"
     * @bodyParam is_activated boolean Whether the driver is activated. Example: true
     * @bodyParam is_online boolean Whether the driver is currently online. Example: false
     * @bodyParam is_suspended boolean Whether the driver is suspended. Example: false
     *
     * @response 200 {
     *   "message": "Driver profile updated",
     *   "driver": {
     *     "id": 1,
     *     "user_id": 25,
     *     "license_number": "DLX-0099",
     *     "is_activated": true,
     *     "is_online": false,
     *     "is_suspended": false
     *   }
     * }
     * @response 404 {
     *   "message": "Driver not found"
     * }
     */
    public function update(Request $request, $id)
    {
        $driver = Driver::find($id);

        if (!$driver) {
            return response()->json(['message' => 'Driver not found'], 404);
        }

        $driver->update($request->only(
            'license_number',
            'is_activated',
            'is_online',
            'is_suspended'
        ));

        return response()->json([
            'message' => 'Driver profile updated',
            'driver' => $driver,
        ]);
    }
}

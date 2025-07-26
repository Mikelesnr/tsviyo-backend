<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use App\Enums\UserRole;

/**
 * @group Admin - Driver Management
 * Endpoints for managing driver profiles. Accessible to admins only.
 */
class DriverAdminController extends Controller
{
    /**
     * Create a driver profile for a user.
     *
     * Only allowed if the user’s role is "driver". Fails if a profile already exists.
     *
     * @authenticated
     * @header Authorization string required Bearer token used to authenticate the request. Example: "Bearer your-token"
     *
     * @bodyParam user_id int required The ID of the user to link to the driver profile. Must be a user with role "driver". Example: 25
     * @bodyParam license_number string required The driver’s license number. Example: "DLX-0071"
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
     * @response 400 {
     *   "message": "User must have the 'driver' role."
     * }
     * @response 409 {
     *   "message": "Driver profile already exists."
     * }
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'license_number' => 'required|string',
        ]);

        $user = User::findOrFail($request->user_id);

        if ($user->role !== UserRole::Driver->value) {
            return response()->json(['message' => "User must have the 'driver' role."], 400);
        }

        if ($user->driver) {
            return response()->json(['message' => "Driver profile already exists."], 409);
        }

        $driver = Driver::create([
            'user_id' => $user->id,
            'license_number' => $request->license_number,
        ]);

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

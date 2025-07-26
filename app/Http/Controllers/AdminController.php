<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Vehicle;
use App\Http\Resources\UserResource;
use App\Http\Resources\VehicleResource;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Display a paginated list of users.
     *
     * @group Admin Management
     * @authenticated
     * @header Authorization string required Bearer token used to authenticate the request. Example: "Bearer your-token"
     * @responseField id integer The ID of the user
     * @responseField name string The user's name
     * @responseField email string The user's email
     */
    public function listUsers(Request $request)
    {
        $users = User::paginate(15);
        return UserResource::collection($users);
    }

    /**
     * Display a paginated list of vehicles.
     *
     * @group Admin Management
     * @authenticated
     * @header Authorization string required Bearer token used to authenticate the request. Example: "Bearer your-token"
     * @responseField id integer The ID of the vehicle
     * @responseField model string Vehicle model name
     * @responseField driver_id integer Linked driver's ID
     */
    public function listVehicles(Request $request)
    {
        $vehicles = Vehicle::paginate(15);
        return VehicleResource::collection($vehicles);
    }

    /**
     * Activate a driver account.
     *
     * @group Admin Management
     * @authenticated
     * @header Authorization string required Bearer token used to authenticate the request. Example: "Bearer your-token"
     * @urlParam driver int required The driver's ID to activate.
     * @response 204 Driver successfully activated
     */
    public function activateDriver($driverId)
    {
        $driver = User::findOrFail($driverId)->driverProfile;
        $driver->update(['is_activated' => true]);
        return response()->noContent();
    }

    /**
     * Deactivate a driver account.
     *
     * @group Admin Management
     * @authenticated
     * @header Authorization string required Bearer token used to authenticate the request. Example: "Bearer your-token"
     * @urlParam driver int required The driver's ID to deactivate.
     * @response 204 Driver successfully deactivated
     */
    public function deactivateDriver($driverId)
    {
        $driver = User::findOrFail($driverId)->driverProfile;
        $driver->update(['is_activated' => false]);
        return response()->noContent();
    }

    /**
     * Suspend a driver from using the service.
     *
     * @group Admin Management
     * @authenticated
     * @header Authorization string required Bearer token used to authenticate the request. Example: "Bearer your-token"
     * @urlParam driver int required The driver's ID to suspend.
     * @response 204 Driver successfully suspended
     */
    public function suspendDriver($driverId)
    {
        $driver = User::findOrFail($driverId)->driverProfile;
        $driver->update(['is_suspended' => true]);
        return response()->noContent();
    }

    /**
     * Unsuspend a previously suspended driver.
     *
     * @group Admin Management
     * @authenticated
     * @header Authorization string required Bearer token used to authenticate the request. Example: "Bearer your-token"
     * @urlParam driver int required The driver's ID to unsuspend.
     * @response 204 Driver successfully unsuspended
     */
    public function unsuspendDriver($driverId)
    {
        $driver = User::findOrFail($driverId)->driverProfile;
        $driver->update(['is_suspended' => false]);
        return response()->noContent();
    }

    /**
     * Delete a user account permanently.
     *
     * @group Admin Management
     * @authenticated
     * @header Authorization string required Bearer token used to authenticate the request. Example: "Bearer your-token"
     * @urlParam user int required The user's ID to delete.
     * @response 204 User successfully deleted
     */
    public function deleteUser($userId)
    {
        $user = User::findOrFail($userId);
        $user->delete();
        return response()->noContent();
    }
}

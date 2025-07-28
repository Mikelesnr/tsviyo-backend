<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\Rider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RiderController extends Controller
{
    /**
     * List the authenticated user's rider profile.
     *
     * @group Rider
     * @authenticated
     * @header Authorization string required Bearer token used to authenticate the request. Example: "Bearer your-token"
     * @response 200 {"data": {"user_id": 1, "home_address": "123 Main St"}}
     */
    public function show()
    {
        $rider = Rider::where('user_id', Auth::id())->first();

        if (!$rider) {
            return response()->json(['message' => 'Rider profile not found'], 404);
        }

        return response()->json(['data' => $rider]);
    }

    /**
     * Create Rider profile.
     *
     * This endpoint allows an authenticated user to create their Rider profile.
     * A home address is required. If a profile image URL is provided, it will be saved.
     *
     * @group Rider
     * @authenticated
     * @header Authorization string required Bearer token used to authenticate the request. Example: "Bearer your-token"
     *
     * @bodyParam home_address string required The rider's home address. Example: 120 Maple Street
     * @bodyParam image_url string The image URL for the rider's profile photo. Must be a valid URL. Example: https://example.com/image.jpg
     *
     * @response 201 {
     *   "message": "Rider profile created.",
     *   "rider": {
     *     "id": 1,
     *     "home_address": "120 Maple Street",
     *     "user_id": 5
     *   }
     * }
     * @response 409 {
     *   "message": "Rider profile already exists."
     * }
     */
    public function create(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'home_address' => 'required|string|max:255',
            'image_url' => 'nullable|url',
        ]);

        if ($user->riderProfile) {
            return response()->json([
                'message' => 'Rider profile already exists.',
            ], 409);
        }

        $rider = Rider::create([
            'user_id' => $user->id,
            'home_address' => $validated['home_address'],
        ]);

        if (!empty($validated['image_url'])) {
            Image::create([
                'url' => $validated['image_url'],
                'type' => 'profile',
                'user_id' => $user->id,
                'vehicle_id' => null,
            ]);
        }

        return response()->json([
            'message' => 'Rider profile created.',
            'rider' => $rider,
        ], 201);
    }

    /**
     * Update Rider profile.
     *
     * This endpoint allows an authenticated user to update their existing Rider profile.
     * A new home address can be provided, and the profile image URL can be updated.
     *
     * @group Rider
     * @authenticated
     * @header Authorization string required Bearer token used to authenticate the request. Example: "Bearer your-token"
     *
     * @bodyParam home_address string required The updated home address. Example: 202 Cedar Lane
     * @bodyParam image_url string The new image URL for the rider's profile. Must be a valid URL. Example: https://example.com/updated-image.jpg
     *
     * @response 200 {
     *   "message": "Rider profile updated.",
     *   "rider": {
     *     "id": 1,
     *     "home_address": "202 Cedar Lane",
     *     "user_id": 5
     *   }
     * }
     * @response 404 {
     *   "message": "No Rider profile found to update."
     * }
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'home_address' => 'required|string|max:255',
            'image_url' => 'nullable|url',
        ]);

        $rider = $user->riderProfile;

        if (!$rider) {
            return response()->json([
                'message' => 'No Rider profile found to update.',
            ], 404);
        }

        $rider->update([
            'home_address' => $validated['home_address'],
        ]);

        if (!empty($validated['image_url'])) {
            Image::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'type' => 'profile',
                    'vehicle_id' => null,
                ],
                [
                    'url' => $validated['image_url'],
                ]
            );
        }

        return response()->json([
            'message' => 'Rider profile updated.',
            'rider' => $rider,
            'image' => Image::where([
                'user_id' => $user->id,
                'type' => 'profile',
                'vehicle_id' => null,
            ])->first()
        ]);
    }

    /**
     * Delete the authenticated user's rider profile.
     *
     * @group Rider
     * @authenticated
     * @header Authorization string required Bearer token used to authenticate the request. Example: "Bearer your-token"
     * @response 200 {"message": "Rider profile deleted"}
     */
    public function destroy()
    {
        $rider = Rider::where('user_id', Auth::id())->first();

        if (!$rider) {
            return response()->json(['message' => 'Rider profile not found'], 404);
        }

        $rider->delete();

        return response()->json(['message' => 'Rider profile deleted']);
    }
}

<?php

namespace App\Http\Controllers;

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
     * Create or update the authenticated user's rider profile.
     *
     * @group Rider
     * @authenticated
     * @header Authorization string required Bearer token used to authenticate the request. Example: "Bearer your-token"
     * @bodyParam home_address string required The rider's home address. Example: 456 Elm St
     * @response 200 {"data": {"user_id": 1, "home_address": "456 Elm St"}}
     */
    public function upsert(Request $request)
    {
        $validated = $request->validate([
            'home_address' => 'required|string|max:255',
        ]);

        $rider = Rider::updateOrCreate(
            ['user_id' => Auth::id()],
            ['home_address' => $validated['home_address']]
        );

        return response()->json(['data' => $rider]);
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

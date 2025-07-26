<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    /**
     * Store a new rating.
     *
     * Submit a review for a completed ride. Authentication is required via a Bearer token.
     *
     * @authenticated
     * @header Authorization string required Bearer token used to authenticate the request. Example: "Bearer {your_token_here}"
     *
     * @bodyParam ride_id integer required The ID of the ride being rated. Example: 7
     * @bodyParam reviewee_id integer required The ID of the user being reviewed. Example: 3
     * @bodyParam stars integer required The star rating (1–5). Example: 5
     *
     * @response 201 {
     *   "id": 25,
     *   "ride_id": 7,
     *   "reviewer_id": 1,
     *   "reviewee_id": 3,
     *   "stars": 5,
     *   "created_at": "2025-07-26T00:35:23.000000Z",
     *   "updated_at": "2025-07-26T00:35:23.000000Z"
     * }
     */
    public function store(Request $request)
    {
        $request->validate([
            'ride_id' => 'required|exists:rides,id',
            'reviewee_id' => 'required|exists:users,id',
            'stars' => 'required|integer|min:1|max:5',
        ]);

        $rating = Rating::create([
            'ride_id' => $request->ride_id,
            'reviewer_id' => Auth::id(),
            'reviewee_id' => $request->reviewee_id,
            'stars' => $request->stars,
        ]);

        return response()->json($rating, 201);
    }

    /**
     * Get all ratings given by the authenticated user.
     *
     * Returns a list of all ratings submitted by the current user.
     * Requires a Bearer token for authentication.
     *
     * @authenticated
     * @header Authorization string required Bearer token used to authenticate the request. Example: "Bearer your-access-token"
     *
     * @response 200 [
     *   {
     *     "id": 25,
     *     "ride_id": 7,
     *     "reviewee_id": 3,
     *     "stars": 5,
     *     "created_at": "2025-07-26T00:35:23.000000Z",
     *     "updated_at": "2025-07-26T00:35:23.000000Z"
     *   }
     * ]
     */
    public function myRatings()
    {
        $ratings = Rating::where('reviewer_id', Auth::id())->with(['ride', 'reviewee'])->get();

        return response()->json($ratings);
    }

    /**
     * Get the average rating received by the authenticated user.
     *
     * Returns the average star rating this user has received from others.
     * Requires a Bearer token for authentication.
     *
     * @authenticated
     * @header Authorization string required Bearer token used to authenticate the request. Example: "Bearer your-access-token"
     *
     * @response 200 {
     *   "average_rating": 4.7
     * }
     */
    public function myAverageRating()
    {
        $avg = Rating::where('reviewee_id', Auth::id())->avg('stars');

        return response()->json([
            'average_rating' => round($avg, 2),
        ]);
    }
}

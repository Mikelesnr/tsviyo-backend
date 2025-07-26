<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;
use App\Models\Payment;
use App\Services\MockPaymentProvider;
use App\Enums\PaymentMethod;

/**
 * @group Rider
 * APIs for riders to simulate and create payments for rides.
 *
 * These endpoints require Sanctum authentication and are only accessible to riders.
 */
class PaymentController extends Controller
{
    /**
     * Simulate and store a ride payment.
     *
     * Sends a mock payment using the MockPaymentProvider and records it in the database.
     *
     * @authenticated
     * @header Authorization string required Bearer token used to authenticate the request. Example: "Bearer your-token"
     *
     * @bodyParam ride_id int required The ID of the ride being paid for. Example: 12
     * @bodyParam amount number required The amount to pay. Must be greater than 0. Example: 15.50
     * @bodyParam method string optional The payment method used. One of: cash, card, mobile. Default is "card". Example: cash
     *
     * @response 201 {
     *  "message": "Payment processed via Mock Provider",
     *  "payment": {
     *      "id": 35,
     *      "ride_id": 12,
     *      "amount": "15.50",
     *      "method": "cash",
     *      "created_at": "2025-07-26T01:58:12.000000Z"
     *  },
     *  "reference": "MOCK-LJ7B9QX1",
     *  "timestamp": "2025-07-26T01:58:12.000000Z"
     * }
     */
    public function store(Request $request, MockPaymentProvider $provider)
    {
        $validated = $request->validate([
            'ride_id' => 'required|exists:rides,id',
            'amount' => 'required|numeric|min:1',
            'method' => ['nullable', new Enum(PaymentMethod::class)],
        ]);

        $result = $provider->pay(
            $validated['ride_id'],
            $validated['amount'],
            $validated['method'] ?? PaymentMethod::CARD->value
        );

        $payment = Payment::create([
            'ride_id' => $result['ride_id'],
            'amount' => $result['amount'],
            'method' => $result['method'],
        ]);

        return response()->json([
            'message' => 'Payment processed via Mock Provider',
            'payment' => $payment,
            'reference' => $result['reference'],
            'timestamp' => $result['timestamp'],
        ], 201);
    }

    /**
     * @group Driver
     * Retrieve a driver’s monthly payment summary.
     *
     * Returns total earnings for the authenticated driver based on completed payments in the current month.
     *
     * @authenticated
     * @header Authorization string required Bearer token used to authenticate the request. Example: "Bearer your-token"
     *
     * @response 200 {
     *  "driver_id": 9,
     *  "month": "July 2025",
     *  "total_earnings": 432.75,
     *  "rides_paid": 6,
     *  "payments": [
     *    {
     *      "id": 21,
     *      "ride_id": 105,
     *      "amount": "72.50",
     *      "method": "card",
     *      "created_at": "2025-07-01T10:15:00Z"
     *    }
     *  ]
     * }
     */
    public function driverMonthlySummary(Request $request)
    {
        $user = $request->user();

        $payments = Payment::with('ride')
            ->whereHas(
                'ride',
                fn($query) =>
                $query->where('driver_id', $user->id)
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
            )
            ->get();

        $total = $payments->sum('amount');

        return response()->json([
            'driver_id' => $user->id,
            'month' => now()->format('F Y'),
            'total_earnings' => $total,
            'rides_paid' => $payments->count(),
            'payments' => $payments,
        ]);
    }
}

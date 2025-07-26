<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class MockPaymentProvider
{
    public function pay(int $rideId, float $amount, string $method = 'fake_card')
    {
        // Simulate an external API call (you can adapt this for /products or /orders)
        $response = Http::get("https://fakeapi.net/products/1");

        if (! $response->successful()) {
            throw new \Exception('Mock payment failed: Invalid product or API error.');
        }

        $product = $response->json();

        return [
            'ride_id' => $rideId,
            'amount' => $amount,
            'method' => $method,
            'reference' => 'MOCK-' . strtoupper(str()->random(8)),
            'status' => 'success',
            'timestamp' => now()
        ];
    }
}

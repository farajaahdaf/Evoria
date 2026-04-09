<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class GoogleMapsService
{
    public function geocode(string $query): ?array
    {
        $apiKey = config('services.google_maps.server_api_key');

        if (blank($apiKey)) {
            return null;
        }

        $response = Http::acceptJson()->get('https://maps.googleapis.com/maps/api/geocode/json', [
            'address' => $query,
            'key' => $apiKey,
        ]);

        if (! $response->successful()) {
            throw new RuntimeException('Google Maps geocoding request failed.');
        }

        $payload = $response->json();

        if (($payload['status'] ?? null) !== 'OK' || empty($payload['results'][0])) {
            return null;
        }

        $result = $payload['results'][0];

        return [
            'address' => $result['formatted_address'] ?? null,
            'latitude' => $result['geometry']['location']['lat'] ?? null,
            'longitude' => $result['geometry']['location']['lng'] ?? null,
        ];
    }
}

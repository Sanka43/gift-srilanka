<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

/**
 * Distance based delivery charge:
 *   distance <= base_km  -> base_fee
 *   otherwise            -> base_fee + ceil(distance - base_km) * per_km_fee
 */
class DeliveryFeeService
{
    public const DEFAULTS = [
        'delivery.enabled' => '1',
        'delivery.start_name' => 'Colombo',
        'delivery.start_lat' => '6.9271',
        'delivery.start_lng' => '79.8612',
        'delivery.base_km' => '5',
        'delivery.base_fee' => '350',
        'delivery.per_km_fee' => '60',
        'delivery.max_radius_km' => '30',
        'delivery.free_above' => '0',
    ];

    public function settings(): array
    {
        $s = [];
        foreach (self::DEFAULTS as $key => $default) {
            $s[substr($key, 9)] = Setting::get($key, $default);
        }

        return [
            'enabled' => (bool) (int) $s['enabled'],
            'start_name' => (string) $s['start_name'],
            'start_lat' => (float) $s['start_lat'],
            'start_lng' => (float) $s['start_lng'],
            'base_km' => (float) $s['base_km'],
            'base_fee' => (float) $s['base_fee'],
            'per_km_fee' => (float) $s['per_km_fee'],
            'max_radius_km' => (float) $s['max_radius_km'],
            'free_above' => (float) $s['free_above'],
        ];
    }

    public function isEnabled(): bool
    {
        return $this->settings()['enabled'];
    }

    /** Charge shown before the customer has picked a location. */
    public function defaultCharge(): float
    {
        $s = $this->settings();

        return $s['enabled'] ? $s['base_fee'] : (float) config('shop.delivery_charge', 350);
    }

    /** Pure formula: charge for a given road distance (km). */
    public function feeForDistance(float $km, float $subtotal = 0): float
    {
        $s = $this->settings();

        if ($s['free_above'] > 0 && $subtotal >= $s['free_above']) {
            return 0.0;
        }

        $extraKm = (int) ceil(max(0, $km - $s['base_km']));

        return round($s['base_fee'] + $extraKm * $s['per_km_fee'], 2);
    }

    /**
     * Full quote for a customer pin. Throws a ValidationException when the pin
     * is outside the delivery radius.
     *
     * @return array{distance_km: float, charge: float, free: bool}
     */
    public function quote(float $lat, float $lng, float $subtotal = 0): array
    {
        $s = $this->settings();
        $km = $this->roadDistanceKm($s['start_lat'], $s['start_lng'], $lat, $lng);

        if ($s['max_radius_km'] > 0 && $km > $s['max_radius_km']) {
            throw ValidationException::withMessages([
                'delivery_lat' => ['Sorry, we deliver within ' . rtrim(rtrim(number_format($s['max_radius_km'], 1), '0'), '.') . ' km only. Please choose a closer location.'],
            ]);
        }

        $charge = $this->feeForDistance($km, $subtotal);

        return [
            'distance_km' => round($km, 2),
            'charge' => $charge,
            'free' => $charge == 0.0,
        ];
    }

    public function roadDistanceKm(float $fromLat, float $fromLng, float $toLat, float $toLng): float
    {
        $key = sprintf('delivery_dist:%.4f,%.4f:%.4f,%.4f', $fromLat, $fromLng, $toLat, $toLng);

        return (float) Cache::remember($key, now()->addDays(7), function () use ($fromLat, $fromLng, $toLat, $toLng) {
            try {
                $km = config('shop.maps.google_server_key')
                    ? $this->googleDistanceKm($fromLat, $fromLng, $toLat, $toLng)
                    : $this->osrmDistanceKm($fromLat, $fromLng, $toLat, $toLng);
                if ($km !== null) {
                    return $km;
                }
            } catch (\Throwable $e) {
                Log::warning('Delivery distance lookup failed, using straight-line fallback', ['message' => $e->getMessage()]);
            }

            return $this->haversineKm($fromLat, $fromLng, $toLat, $toLng) * (float) config('shop.maps.fallback_road_factor', 1.3);
        });
    }

    private function osrmDistanceKm(float $fromLat, float $fromLng, float $toLat, float $toLng): ?float
    {
        $base = rtrim((string) config('shop.maps.osrm_url'), '/');
        $res = Http::timeout(6)->acceptJson()
            ->get("{$base}/route/v1/driving/{$fromLng},{$fromLat};{$toLng},{$toLat}", ['overview' => 'false']);

        $meters = $res->json('routes.0.distance');

        return $res->successful() && is_numeric($meters) ? $meters / 1000 : null;
    }

    private function googleDistanceKm(float $fromLat, float $fromLng, float $toLat, float $toLng): ?float
    {
        $res = Http::timeout(6)->withHeaders([
            'X-Goog-Api-Key' => config('shop.maps.google_server_key'),
            'X-Goog-FieldMask' => 'routes.distanceMeters',
        ])->post('https://routes.googleapis.com/directions/v2:computeRoutes', [
            'origin' => ['location' => ['latLng' => ['latitude' => $fromLat, 'longitude' => $fromLng]]],
            'destination' => ['location' => ['latLng' => ['latitude' => $toLat, 'longitude' => $toLng]]],
            'travelMode' => 'DRIVE',
        ]);

        $meters = $res->json('routes.0.distanceMeters');

        return $res->successful() && is_numeric($meters) ? $meters / 1000 : null;
    }

    public function haversineKm(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $r = 6371.0;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return 2 * $r * asin(min(1, sqrt($a)));
    }
}

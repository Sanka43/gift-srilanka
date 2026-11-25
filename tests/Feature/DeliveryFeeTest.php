<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Services\DeliveryFeeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class DeliveryFeeTest extends TestCase
{
    use RefreshDatabase;

    private function service(array $overrides = []): DeliveryFeeService
    {
        Setting::setMany(array_merge([
            'delivery.base_km' => 5,
            'delivery.base_fee' => 350,
            'delivery.per_km_fee' => 60,
            'delivery.max_radius_km' => 30,
            'delivery.free_above' => 0,
        ], $overrides));

        return app(DeliveryFeeService::class);
    }

    public function test_within_base_distance_charges_base_fee(): void
    {
        $this->assertSame(350.0, $this->service()->feeForDistance(3.2));
        $this->assertSame(350.0, $this->service()->feeForDistance(5.0));
    }

    public function test_extra_km_rounds_up_to_full_km(): void
    {
        $s = $this->service();
        $this->assertSame(410.0, $s->feeForDistance(5.1));   // 1 extra km
        $this->assertSame(830.0, $s->feeForDistance(12.3));  // ceil(7.3)=8 -> 350 + 480
    }

    public function test_free_delivery_threshold(): void
    {
        $s = $this->service(['delivery.free_above' => 5000]);
        $this->assertSame(0.0, $s->feeForDistance(12.3, 5000));
        $this->assertSame(830.0, $s->feeForDistance(12.3, 4999));
    }

    public function test_quote_uses_routing_distance(): void
    {
        Http::fake(['*' => Http::response(['routes' => [['distance' => 12300]]])]);
        $quote = $this->service()->quote(6.95, 79.9);
        $this->assertSame(12.3, $quote['distance_km']);
        $this->assertSame(830.0, $quote['charge']);
    }

    public function test_quote_rejects_pin_outside_radius(): void
    {
        Http::fake(['*' => Http::response(['routes' => [['distance' => 45000]]])]);
        $this->expectException(ValidationException::class);
        $this->service()->quote(7.2, 80.2);
    }

    public function test_routing_failure_falls_back_to_straight_line(): void
    {
        Http::fake(['*' => Http::response([], 500)]);
        $km = $this->service()->roadDistanceKm(6.9271, 79.8612, 6.9371, 79.8612);
        $this->assertGreaterThan(1.0, $km);
        $this->assertLessThan(2.0, $km);
    }
}

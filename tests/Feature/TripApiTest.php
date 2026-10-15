<?php

namespace Tests\Feature;

use App\Models\Country;
use App\Models\Trip;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TripApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_a_trip_with_countries(): void
    {
        $countries = Country::factory()->count(2)->create();

        $this->postJson('/api/trips', [
            'available_seats' => 8,
            'country_ids'     => $countries->pluck('id')->all(),
        ])
            ->assertCreated()
            ->assertJsonPath('data.available_seats', 8)
            ->assertJsonCount(2, 'data.countries');
    }

    public function test_trip_requires_at_least_one_country(): void
    {
        $this->postJson('/api/trips', ['available_seats' => 5, 'country_ids' => []])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('country_ids');
    }

    public function test_trip_rejects_unknown_countries(): void
    {
        $this->postJson('/api/trips', ['available_seats' => 5, 'country_ids' => [999]])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('country_ids.0');
    }

    public function test_seats_must_be_a_non_negative_integer(): void
    {
        $country = Country::factory()->create();

        $this->postJson('/api/trips', ['available_seats' => -1, 'country_ids' => [$country->id]])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('available_seats');
    }

    public function test_can_list_trips(): void
    {
        Trip::factory()->count(3)->create();

        $this->getJson('/api/trips')
            ->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_can_filter_trips_by_country(): void
    {
        $japan = Country::factory()->create();
        $peru = Country::factory()->create();

        Trip::factory()->create()->countries()->attach($japan);
        Trip::factory()->create()->countries()->attach($peru);

        $this->getJson("/api/trips?country_id={$japan->id}")
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_can_filter_trips_by_minimum_seats(): void
    {
        Trip::factory()->create(['available_seats' => 2]);
        Trip::factory()->create(['available_seats' => 10]);

        $this->getJson('/api/trips?min_seats=5')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.available_seats', 10);
    }

    public function test_can_combine_filters(): void
    {
        $japan = Country::factory()->create();

        Trip::factory()->create(['available_seats' => 2])->countries()->attach($japan);
        Trip::factory()->create(['available_seats' => 10])->countries()->attach($japan);
        Trip::factory()->create(['available_seats' => 10]);

        $this->getJson("/api/trips?country_id={$japan->id}&min_seats=5")
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_can_update_a_trip_partially(): void
    {
        $country = Country::factory()->create();
        $trip = Trip::factory()->create(['available_seats' => 10]);
        $trip->countries()->attach($country);

        $this->patchJson("/api/trips/{$trip->id}", ['available_seats' => 3])
            ->assertOk()
            ->assertJsonPath('data.available_seats', 3)
            ->assertJsonCount(1, 'data.countries');
    }

    public function test_can_change_trip_countries(): void
    {
        $old = Country::factory()->create();
        $new = Country::factory()->create();
        $trip = Trip::factory()->create();
        $trip->countries()->attach($old);

        $this->putJson("/api/trips/{$trip->id}", [
            'available_seats' => $trip->available_seats,
            'country_ids'     => [$new->id],
        ])
            ->assertOk()
            ->assertJsonPath('data.countries.0.id', $new->id);
    }

    public function test_can_delete_a_trip(): void
    {
        $trip = Trip::factory()->create();

        $this->deleteJson("/api/trips/{$trip->id}")->assertNoContent();

        $this->assertDatabaseMissing('trips', ['id' => $trip->id]);
    }

    public function test_show_returns_404_when_missing(): void
    {
        $this->getJson('/api/trips/999')->assertNotFound();
    }
}
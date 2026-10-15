<?php

namespace Tests\Feature;

use App\Models\Country;
use App\Models\Trip;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CountryApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_countries(): void
    {
        Country::factory()->count(3)->create();
        $this->getJson('/api/countries')
        ->assertOk()
        ->assertJsonCount(3, 'data');
    }

    public function test_can_create_a_country(): void
    {
        $this->postJson('/api/countries', ['name' => 'Giappone'])
            ->assertCreated()
            ->assertJsonPath('data.name', 'Giappone');

        $this->assertDatabaseHas('countries', ['name' => 'Giappone']);
    }

    public function test_cannot_create_duplicate_country(): void
    {
        Country::factory()->create(['name' => 'Giappone']);

        $this->postJson('/api/countries', ['name', 'Giappone'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('name');
    }

    public function test_name_is_required(): void{
        $this->postJson('/api/countries', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('name');
    }

    public function test_can_show_a_country(): void{
        $country = Country::factory()->create();

        $this->getJson("/api/countries/{$country->id}")
        ->assertOk()
        ->assertJsonPath('data.id', $country->id);
    }

    public function test_show_returns_404_when_missing(): void {
        $this->getJson('/api/countries/999')->assertNotFound();
    }

    public function test_can_update_a_country(): void{
        $country = Country::factory()->create(['name'=>'Vietnam']);

        $this->putJson("/api/countries/{$country->id}", ['name' => 'Thailandia'])
        ->assertOk()
        ->assertJsonPath('data.name', 'Thailandia');
    }

    public function test_can_update_a_country_keeping_its_own_name(): void{
        $country = Country::factory()->create(['name' => 'Vietnam']);

        $this->putJson("/api/countries/{$country->id}", ['name' => 'Vietnam'])
        ->assertOk();
    }


    public function test_can_delete_an_unused_country(): void{
        $country = Country::factory()->create();

        $this->deleteJson("/api/countries/{$country->id}")->assertNoContent();
        $this->assertDatabaseMissing('countries', ['id' => $country->id]);
    }

    public function test_cannot_delete_a_country_used_by_a_trip(): void{
        $country = Country::factory()->create();
        $trip = Trip::factory()->create();
        $trip->countries()->attach($country);

        $this->deleteJson("/api/countries/{$country->id}")->assertConflict();

        $this->assertDatabaseHas('countries', ['id' => $country->id]);
    }
}

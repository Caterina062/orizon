<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Trip;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TripSeeder extends Seeder
{
    public function run(): void
    {
        $countryIds = Country::puck('id');

        Trip::factory()
        ->count(12)
        ->create()
        ->each(function (Trip $trip) use ($countryIds) {
            $trip->countries()->attach(
                $countryIds->random(rand(1, 3))->all()
            );
        });
    }
}

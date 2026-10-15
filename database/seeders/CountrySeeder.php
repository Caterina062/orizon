<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    // la traccia richiede Asia e America 
    public function run(): void
    {
        $countries = [
            //Asia
            'Giappone', 'Vietnam', 'Thailandia', 'Cambogia', 'Laos',
            'Nepal', 'India', 'Sri Lanka', 'Indonesia', 'Filippine',
            //America
            'Perù', 'Bolivia', 'Colombia', 'Costa Rica', 'Messico',
            'Guatemala', 'Ecuador', 'Argentina', 'Cile', 'Canada',
        ];

        foreach ($countries as $name) {
            Country::firstOrCreate(['name' => $name]);
        }
    }
}

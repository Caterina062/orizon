<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCountryRequest;
use App\Http\Requests\UpdateCountryRequest;
use App\Http\Resources\CountryResource;
use App\Models\Country;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class CountryContropper extends Controller
{
    public function index()
    {
        return CountryResource::collection(Country::orderBy('name')->get());
    }

    public function store(StoreCountryRequest $request)
    {
        $country = Country::create($request->validated());

        return new CountryResource($country);
    }

    public function show(Country $country): CountryResource
    {
        return new CountryResource($country);
    }

   public function update(UpdateCountryRequest $request, Country $country): CountryResource
    {
        $country->update($request->validated());

        return new CountryResource($country);
    }

   public function destroy(Country $country): JsonResponse|Response
    {
        if ($country->trips()->exists()) {
            return response()->json([
                'message' => 'Il paese è associato ad almeno un viaggio e non può essere eliminato.',
            ], 409);
        }

        $country->delete();

        return response()->noContent();
    }
}

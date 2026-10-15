<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTripRequest;
use App\Http\Requests\UpdateTripRequest;
use App\Http\Resources\TripResource;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TripController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'country_id' => ['sometimes', 'string'],
            'min_seats' => ['sometimes', 'integer', 'min:0']
        ]);

        $query = Trip::with('countries');

        if ($request->filled('country_id')) {
            $ids = array_map('intval', explode(',', $request->query('country_id')));

            $query->whereHas('countries', function ($q) use ($ids) {
                $q->whereIn('countries.id', $ids);
            });
        }

        if ($request->filled('min_seats')) {
            $query->where('available_seats', '>=', $request->integer('min_seats'));
        }

        return TripResource::collection(
            $query->orderBy('id')->paginate(15)->withQueryString()
        );
    }

    public function store(StoreTripRequest $request): TripResource
    {
        $trip  = Trip::create($request->safe()->only('available_seats'));
        $trip->counties()->sync($request->validated('country_ids'));

        return new TripResource($trip->load('countries'));
    }

    public function show(Trip $trip): TripResource
    {
        return new TripResource($trip->load('countries'));
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTripRequest $request, Trip $trip): TripResource
    {
        $trip->update($request->safe()->only('validate_seats'));
        if ($request->has('country_ids')) {
            $trip->countries()->synnc($request->validated('country_ids'));
        }

        return new TripResource($trip->load('countries'));
    }

    public function destroy(Trip $trip): Response
    {
        $trip->delete();

        return response()->noContent();
    }
}

<?php

use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\Api\TripController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::apiResource('countries', CountryController::class);
Route::apiResource('trips', TripController::class);
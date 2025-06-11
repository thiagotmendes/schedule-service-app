<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProviderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ServiceController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('clients', ClientController::class);
Route::apiResource('services', ServiceController::class);
Route::apiResource('providers', ProviderController::class);

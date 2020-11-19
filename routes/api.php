<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApartmentBuildingController;
use App\Http\Controllers\DistrictController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('apartment_buildings',[ApartmentBuildingController::class, 'index']);
Route::get('apartment_buildings/{id}', [ApartmentBuildingController::class, 'show']);
Route::post('apartment_buildings', [ApartmentBuildingController::class, 'store']);
Route::put('apartment_buildings/{id}', [ApartmentBuildingController::class, 'update']);
Route::delete('apartment_buildings/{id}', [ApartmentBuildingController::class, 'delete']);

Route::get('districts',[DistrictController::class, 'index']);

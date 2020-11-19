<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ApartmentBuilding;
use App\Services\ApartmentBuildingService;

class ApartmentBuildingController extends Controller
{

    public function __construct(ApartmentBuildingService $apartmentBuildingService)
    {
        $this->apartmentBuildingService = $apartmentBuildingService;
    }

    public function index()
    {
        return $this->apartmentBuildingService->getAll();
    }

    public function show($id)
    {
        $apartmentBuilding = $this->apartmentBuildingService->getById($id);
        return $apartmentBuilding;
    }

    public function store(Request $request)
    {
        $apartmentBuilding = ApartmentBuilding::create($request->all());
        return response()->json($apartmentBuilding, 201);
    }

    public function update(Request $request, $id)
    {
        $apartmentBuilding = ApartmentBuilding::findOrFail($id);
        $apartmentBuilding->update($request->all());

        return response()->json($apartmentBuilding, 200);
    }

    public function delete(Request $request, $id)
    {
        $apartmentBuilding = ApartmentBuilding::findOrFail($id);
        $apartmentBuilding->delete();

        return response()->json(null, 204);
    }
}

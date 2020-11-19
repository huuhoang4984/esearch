<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ApartmentBuilding;

class ApartmentBuildingController extends Controller
{

    public function index()
    {
        return ApartmentBuilding::all();
    }

    public function show($id)
    {
        $apartmentBuilding = ApartmentBuilding::find($id);
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

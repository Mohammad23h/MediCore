<?php

namespace App\Http\Controllers;

use App\Models\MedicalSupply;
use Illuminate\Http\Request;

class MedicalSupplyController extends Controller
{
    public function index()
    {
        return response()->json(MedicalSupply::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'quantity' => 'required|integer|min:0',
            'clinic_id' => 'nullable|exists:clinics,id',
            'center_id' => 'nullable|exists:centers,id',
        ]);

        return response()->json(MedicalSupply::create($validated), 201);
    }

    public function show($id)
    {
        return response()->json(MedicalSupply::with(['clinic', 'center'])->findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $supply = MedicalSupply::findOrFail($id);
        $supply->update($request->all());
        return response()->json($supply);
    }

    public function destroy($id)
    {
        MedicalSupply::destroy($id);
        return response()->json(['message' => 'Deleted']);
    }
}

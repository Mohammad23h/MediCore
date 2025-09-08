<?php

namespace App\Http\Controllers;

use App\Models\PrescriptionMedicines;
use Illuminate\Http\Request;

class PrescriptionMedicinesController extends Controller
{
    //
    public function index()
    {
        return response()->json(PrescriptionMedicines::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'prescription_id' => 'required||exists:prescriptions,id',
            'medicine_name' => 'required|string',
            'dosage' => 'nullable|string',
            'frequency' => 'nullable|string',
            'notes' => 'nullable|max:255',
        ]);

        return response()->json(PrescriptionMedicines::create($validated), 201);
    }

    public function show($id)
    {
        return response()->json(PrescriptionMedicines::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $illness = PrescriptionMedicines::findOrFail($id);
        $illness->update($request->all());
        return response()->json($illness);
    }

    public function destroy($id)
    {
        PrescriptionMedicines::destroy($id);
        return response()->json(['message' => 'Deleted']);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\PatientIllness;
use Illuminate\Http\Request;

class PatientIllnessController extends Controller
{
    public function index()
    {
        return response()->json(PatientIllness::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'illness_id' => 'required|exists:illnesses,id',
            'diagnosed_at' => 'nullable|date',
        ]);

        return response()->json(PatientIllness::create($validated), 201);
    }

    public function show($id)
    {
        return response()->json(PatientIllness::with(['patient', 'illness'])->findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $record = PatientIllness::findOrFail($id);
        $record->update($request->all());
        return response()->json($record);
    }

    public function destroy($id)
    {
        PatientIllness::destroy($id);
        return response()->json(['message' => 'Deleted']);
    }
}

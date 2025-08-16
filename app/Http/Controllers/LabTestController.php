<?php

namespace App\Http\Controllers;

use App\Models\LabTest;
use Illuminate\Http\Request;

class LabTestController extends Controller
{
    public function index()
    {
        return response()->json(LabTest::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'laboratory_id' => 'required|exists:laboratories,id',
            'test_type' => 'required|string',
            'result' => 'nullable|string',
            'date' => 'required|date',
        ]);

        return response()->json(LabTest::create($validated), 201);
    }

    public function show($id)
    {
        return response()->json(LabTest::with(['patient', 'laboratory'])->findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $labTest = LabTest::findOrFail($id);
        $labTest->update($request->all());
        return response()->json($labTest);
    }

    public function destroy($id)
    {
        LabTest::destroy($id);
        return response()->json(['message' => 'Deleted']);
    }
}

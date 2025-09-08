<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use Illuminate\Http\Request;

class PrescriptionController extends Controller
{
    //
    public function index()
    {
        return response()->json(Prescription::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required||exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'prescription_date' => 'nullable|date',
        ]);

        return response()->json(Prescription::create($validated), 201);
    }

    public function show($id)
    {
        return response()->json(Prescription::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $illness = Prescription::findOrFail($id);
        $illness->update($request->all());
        return response()->json($illness);
    }

    public function destroy($id)
    {
        Prescription::destroy($id);
        return response()->json(['message' => 'Deleted']);
    }
}

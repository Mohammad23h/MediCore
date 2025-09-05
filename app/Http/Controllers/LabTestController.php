<?php

namespace App\Http\Controllers;

use App\Models\LabTest;
use Illuminate\Http\Request;
use App\Models\Assistant;
use App\Models\Patient;

class LabTestController extends Controller
{
    public function index()
    {
        return response()->json(LabTest::all());
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id'=> 'required|exists:patients,id',
            'assistant_id' => 'required|exists:assistants,id',
            'test_type' => 'required|string',
            'test_date' => 'required|date',
            'lab_id'=> 'required|exists:laboratories,id',
            'result' => 'nullable|string',
            'pdf_file' => 'nullable|mimes:pdf|max:5120', 
        ]);

        $path = null;
        if ($request->hasFile('pdf_file')) {
            $path = $request->file('pdf_file')->store('lab_tests', 'public');
        }

        $labTest = LabTest::create([
            'patient_id'   => $validated['patient_id'],
            'assistant_id' => $validated['assistant_id'],
            'test_type'    => $validated['test_type'],
            'pdf_file_uri' => $path ? Storage::url($path) : null,
            'test_date'    => $validated['test_date'],
            'lab_id'       => $validated['lab_id'],
            'result'       => $validated['result'] ?? null,
        ]);

        return response()->json([
            'message' => 'Lab test saved successfully',
            'data'    => $labTest
        ], 201);
    }




    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'lab_id' => 'required|exists:laboratories,id',
            'test_type' => 'required|string',
            'result' => 'nullable|string',
            'test_date' => 'required|date',
        ]);

          $assistant = Assistant::where('user_id', auth()->id())->first();

        if (!$assistant) {
            return response()->json(['message' => 'Assistant not found.'], 404);
        }
        $validated['assistant_id'] = $assistant->id;

        
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

    public function searchByPatientName(Request $request) {

          $name = $request->input('name');
          
          $patient = Patient::where('name', 'LIKE', "%{$name}%")->first();
          if (!$patient) {
              return response()->json(['message' => 'Patient not found'], 404);
          }
          
          $labTests = LabTest::where('patient_id', $patient->id)->get();
          if ($labTests->isEmpty()) {
              return response()->json(['message' => 'No lab tests found for this patient'], 404);
          }

          return response()->json($labTests);
    }

    
}

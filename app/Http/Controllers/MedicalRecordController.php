<?php

namespace App\Http\Controllers;

use App\Models\Assistant;
use App\Models\MedicalRecord;
use App\Models\User;
use App\Models\Patient;
use Illuminate\Http\Request;

class MedicalRecordController extends Controller
{
    public function index()
    {
        return response()->json(MedicalRecord::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            //'assistant_id' => 'required|exists:assistants,id',
            'diagnosis' => 'required|string',
            'notes' => 'nullable|string',
            'date' => 'required|date',
        ]);
        
        $assistant = Assistant::where('user_id', auth()->id())->first();

        if (!$assistant) {
            return response()->json(['message' => 'Assistant not found.'], 404);
        }
        $validated['assistant_id'] = $assistant->id;

        return response()->json(MedicalRecord::create($validated), 201);

        // $validated['assistant_id'] = Assistant::firstWhere('user_id' , '==' , auth()->id())->id;
        // return response()->json(MedicalRecord::create($validated), 201);
    }

    public function show($id)
    {
        return response()->json(MedicalRecord::with('patient')->findOrFail($id));
    }

    

    public function update(Request $request, $id)
    {
        $record = MedicalRecord::findOrFail($id);
        if($record->assistant->user_id !== auth()->user()->id) {
            return response()->json(['message' => 'Access Denied'],403);
        }
        $Success = $record->update($request->all());
        if(!$Success){
            return response()->json(['message' => 'Failed'],400);
        }
        return response()->json($record , 200);
    }

    public function destroy($id)
    {
        MedicalRecord::destroy($id);
        return response()->json(['message' => 'Deleted']);
    }

      public function searchByPatientName(Request $request) {

          $name = $request->input('name');
          
          $patient = Patient::where('name', 'LIKE', "%{$name}%")->first();
          if (!$patient) {
              return response()->json(['message' => 'Patient not found'], 404);
          }
          
          $record = MedicalRecord::where('patient_id', $patient->id)->get();
          if ($record->isEmpty()) {
              return response()->json(['message' => 'No records found for this patient'], 404);
          }

          return response()->json($record);
    }
}

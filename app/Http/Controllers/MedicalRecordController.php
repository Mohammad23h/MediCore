<?php

namespace App\Http\Controllers;

use App\Models\Assistant;
use App\Models\MedicalRecord;
use App\Models\User;
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
        $validated['assistant_id'] = Assistant::firstWhere('user_id' , '==' , auth()->id())->id;
        return response()->json(MedicalRecord::create($validated), 201);
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
}

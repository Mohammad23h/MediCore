<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function index() { return response()->json(Patient::all()->makeHidden('user_id')); }
    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required', 
            'phone' => 'nullable|numeric',
            'gender' => 'nullable',
            'date_of_birth' => 'nullable|date',
            //'image_url'=> 'url|nullable',
            "blood_type" => 'string|nullable'
        ]);
        $validated['user_id'] = auth()->id();
        //$validated['image_url'] = $this->UploadImage($request,'doctors');
        $validated['registered_at'] = now();
        return response()->json(Patient::create($validated), 201);
    }
    public function show($id) { 
        return response()->json(Patient::with(['appointments'])->findOrFail($id)); 
    }

    public function showMyProfile() { 
        return response()->json(Patient::with(['appointments'])->firstWhere('user_id',auth()->id())); 
    }
    public function update(Request $request, $id) {
        $patient = Patient::findOrFail($id);
        if($patient->user_id !== auth()->id()) {
            return response()->json(['message' => 'Access Denied'],403);
        }
        $Success = $patient->update($request->all());
        if(!$Success){
            return response()->json(['message' => 'Failed'],400);
        }
        return response()->json($patient , 200);
    }
    public function destroy($id) {
        Patient::destroy($id);
        return response()->json(['message' => 'Deleted']);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function index() { return response()->json(Patient::all()->makeHidden('user_id')); }
    public function store(Request $request) {
        $patient = Patient::firstWhere('user_id',auth()->id());
        if($patient){
            return response()->json([ 'message' => 'you have an account already'] , 400);
        }
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

        $Success = $patient->update($request->all());
        if(!$Success){
            return response()->json(['message' => 'Failed'],400);
        }
        return response()->json($patient , 200);
    }

    public function updateMyProfile(Request $request) {
        $userId =  auth()->id();
        $patient = Patient::firstWhere('user_id', $userId);
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

    public function destroyMyProfile() {
        $patient = Patient::firstWhere('user_id' , auth()->id());
        $id = $patient->id;
        Patient::destroy($id);
        $userId  = auth()->id();
        User::destroy($userId);
        return response()->json(['message' => 'Your account and your profile has been deleted']);
    }
}

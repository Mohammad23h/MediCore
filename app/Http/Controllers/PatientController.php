<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Storage;

class PatientController extends Controller
{
    public function index() { return response()->json(Patient::all()->makeHidden('user_id')); }
/*
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
*/


public function store(Request $request)
{
    $patient = Patient::firstWhere('user_id', auth()->id());
    if ($patient) {
        return response()->json(['message' => 'you have an account already'], 400);
    }

    $validated = $request->validate([
        'name'         => 'required',
        'phone'        => 'nullable|numeric',
        'gender'       => 'nullable',
        'date_of_birth'=> 'nullable|date',
        'image'        => 'nullable', // صورة ملف فقط
        'blood_type'   => 'string|nullable'
    ]);

    $validated['user_id']       = auth()->id();
    $validated['registered_at'] = now();

    $imageUrl = null;

    if ($request->hasFile('image')) {
        $file     = $request->file('image');
        $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('patients'), $fileName);

        // 🔗 رابط مباشر للصورة
        $imageUrl = url('patients/' . $fileName);
    } elseif ($request->filled('image')) {
        $imageData = $request->input('image');

        if (strpos($imageData, 'base64,') !== false) {
            $imageData = explode('base64,', $imageData)[1];
        }

        $imageData = base64_decode($imageData);

        $fileName = uniqid() . '.png';
        $filePath = public_path('patients/' . $fileName);

        file_put_contents($filePath, $imageData);

        $imageUrl = url('patients/' . $fileName);
    }

    $validated['image_url'] = $imageUrl;
    $patient = Patient::create($validated);

    return response()->json([
        'message' => 'Patient created successfully',
        'data'    => $patient
    ], 201);
}


/*
public function store(Request $request)
{
    $patient = Patient::firstWhere('user_id', auth()->id());
    if ($patient) {
        return response()->json(['message' => 'you have an account already'], 400);
    }

    $validated = $request->validate([
        'name'          => 'required',
        'phone'         => 'nullable|numeric',
        'gender'        => 'nullable',
        'date_of_birth' => 'nullable|date',
        'image'         => 'nullable', // ملف أو base64
        'blood_type'    => 'string|nullable'
    ]);

    $validated['user_id'] = auth()->id();
    $validated['registered_at'] = now();

    // 📌 معالجة الصورة (ملف أو base64)
    if ($request->hasFile('image')) {
        // 🔹 في حال رفعها كملف
        $path = $request->file('image')->store('patients', 'public');
        $validated['image_url'] = asset('storage/' . $path);
    } elseif ($request->filled('image')) {
        // 🔹 في حال إرسالها كـ base64
        $imageData = $request->input('image');

        if (strpos($imageData, 'base64,') !== false) {
            $imageData = explode('base64,', $imageData)[1];
        }

        $imageData = base64_decode($imageData);
        $fileName = uniqid() . '.png';
        $path = "patients/{$fileName}";

        Storage::disk('public')->put($path, $imageData);
        $validated['image_url'] = asset('storage/' . $path);
    }

    $patient = Patient::create($validated);

    return response()->json($patient, 201);
}

*/
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

<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Traits\UploadImageTrait;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    use UploadImageTrait;
    /**
     * Display a listing of the resource.
     */
    public function index() {
        return response()->json(Doctor::all()->makeHidden('user_id')); 
    }
    public function upload(Request $request) {
        return $this->UploadImage($request,'doctors');
    }

    public function store(Request $request) {

        $validated = $request->validate([
            'name' => 'required',
            //'image_url' => 'string|url',
            'start_day' => 'string',
            'end_day' => 'string',
            'start_time' => 'date_format:H:i',
            'end_time' => 'date_format:H:i',
            //'clinic_id' => 'required|exists:clinics,id',
            'specialty' => 'required|string',
            //'user_id' => 'required|exists:users,id'
        ]);
        $validated['user_id'] = auth()->id();
        $validated['image_url'] = $this->UploadImage($request,'doctors');
        return response()->json(Doctor::create($validated), 201);
    }

    public function addCertificates(Request $request, $id)
    {
        $validated = $request->validate([
            'certificates' => 'required|array',
            'certificates.*' => 'string',
        ]);

        $doctor = Doctor::findOrFail($id);

        $current = $doctor->certificates ?? [];
        $merged = array_merge($current, $validated['certificates']);

        $doctor->update([
           'certificates' => $merged
        ]);

        return response()->json([
            'message' => 'Certificates added successfully',
            'certificates' => $doctor->certificates
        ]);
    }


        public function addService(Request $request, $doctorId)
    {
        $request->validate([
            'services' => 'required|array',
            'services.*' => 'exists:services,id'
        ]);
    
        $doctor = Doctor::findOrFail($doctorId);

        $doctor->services()->attach($request->services);
    
        return response()->json([
            'doctor' => $doctor->load('services')
        ], 200);
    }

    public function show($id) {
        $doctor = Doctor::With('clinic' , 'services')->findOrFail($id)->makeHidden('user_id');
        return response()->json($doctor); 
    }

    public function showMyProfile() {
        $doctor = Doctor::With('clinic')->firstWhere('user_id',auth()->id());
        if (!$doctor) {
            return response()->json(['message' => 'Doctor profile not found'], 404);
        }
        return response()->json($doctor); 
    }
    public function update(Request $request, $id) {
        $doctor = Doctor::findOrFail($id);
        if($doctor->user_id !== auth()->id()) {
            return response()->json(['message' => 'Access Denied '],403);
        }
/*
        $validated = $request->validate([
            'name' => 'required',
            'image_url' => 'string|url',
            'start_day' => 'string',
            'end_day' => 'string',
            'start_time' => 'date_format:H:i',
            'end_time' => 'date_format:H:i',
            'clinic_id' => 'required|exists:clinics,id'
        ]);*/

        $Success = $doctor->update($request->all());/*Doctor::update(array_merge($request->all(), [ 'id' => $id]));*/
        
        if(!$Success){
            return response()->json(['message' => 'Failed'],400);
        }
        return response()->json($doctor , 200);
    }
    public function destroy($id) {
        Doctor::destroy($id);
        return response()->json(['message' => 'Deleted']);
    }

    public function addToClinic(Request $request) {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'clinic_id' => 'required|exists:clinics,id',
        ]);
        $doctor = Doctor::findOrFail($request->doctor_id);
        $doctor->clinic_id = $request->clinic_id;
        $doctor->save();
        return response()->json(['message' => 'Succeed']);
    }

    public function destroyMyProfile($id) {
        $doctor = Doctor::findOrFail($id);
        if($doctor->user_id !== auth()->id()) {
            return response()->json(['message' => 'Access Denied'],403);
        }
        Doctor::destroy($id);
        return response()->json(['message' => 'Deleted']);
    }

    public function block($id){
        /*
        if(auth()->user()->role != 'admin'){
            return response()->json(['message' => 'unauthorize'] , 403);
        }
            */
        $doctor = Doctor::with('user')->findOrFail($id);
        $user = $doctor->user;
        $user->blocked = true;
        $user->save();
        return response()->json(['blocked_doctor' => $doctor ], 200);
    }

    public function unBlock($id){
        /*
        if(auth()->user()->role != 'admin'){
            return response()->json(['message' => 'unauthorize'] , 403);
        }
            */
        $doctor = Doctor::with('user')->findOrFail($id);
        $user = $doctor->user;
        $user->blocked = false;
        $user->save();
        return response()->json(['unBlocked_doctor' => $doctor ], 200);
    }




}

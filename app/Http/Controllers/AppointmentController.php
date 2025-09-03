<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() {
         return response()->json(Appointment::with(['doctor','patient'])->get()); 
    }

    public function GetAllInDate(Request $request) {
         return response()->json(Appointment::with(['doctor','patient'])->where('date' , $request->date)); 
    }

    public function getAllClinicAppointment(Request $request,$id) {
         return response()->json(Appointment::with(['doctor','patient'])->where('clinic_id' , $id)->get()); 
    }

    public function getAllDoctorAppointment(Request $request,$id) {
         return response()->json(Appointment::with(['doctor','patient'])->where('doctor_id' , $id)->get()); 
    }

    public function getClinicAppointmentInDate(Request $request,$id) {
         return response()->json(Appointment::with(['doctor','patient'])->where('clinic_id' , $id)->where('date' , $request->date)->get()); 
    }

    public function getDoctorAppointmentInDate(Request $request,$id) {
         return response()->json(Appointment::with(['doctor','patient'])->where('doctor_id' , $id)->where('date' , $request->date)->get()); 
    }

    public function getClinicAppointment($id) {
         return response()->json(Appointment::with(['doctor','patient'])->where('clinic_id', '==' , $id)->where('status' ,'!==', 'done')); 
    }
    public function store(Request $request) {
        $validated = $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'patient_id' => 'required|exists:patients,id',
            'clinic_id' => 'required|exists:clinics,id',
            'date' => 'required|date',
            'time' => 'required',
            'status' => 'required'
        ]);
        $preAppointment = Appointment::where('date', $request->date)->where('time' , $request->time)->firstWhere('clinic_id',$request->clinic_id);
        if(!$preAppointment){
            return response()->json(Appointment::create($validated), 201);
        }
        return response()->json(['message' => 'this time had already selected'] , 400);
    }

    public function show($id) {
       return response()->json(Appointment::findOrFail($id)); 
    }

    public function update(Request $request, $id) {
        $appointment = Appointment::findOrFail($id);
        $Success = $appointment->update($request->all());
        if(!$Success){
            return response()->json(['message' => 'Failed'],400);
        }
        return response()->json($appointment);
    }
    public function destroy($id) {
        Appointment::destroy($id);
        return response()->json(['message' => 'Deleted']);
    }
}

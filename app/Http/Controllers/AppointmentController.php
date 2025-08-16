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
         return response()->json(Appointment::with(['doctor','patient'])->all()); 
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
        return response()->json(Appointment::create($validated), 201);
    }
    public function show($id) { return response()->json(Appointment::findOrFail($id)); }
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

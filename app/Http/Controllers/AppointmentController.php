<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\Patient;
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
        $today = now()->startOfDay();
        $endDate = now()->addDays(10)->endOfDay();
         return response()->json(Appointment::with(['doctor','patient'])->where('clinic_id' , $id)->whereBetween('date', [$today, $endDate])->get()); 
    }

    public function getAllDoctorAppointment(Request $request,$id) {
        $today = now()->startOfDay();
        $endDate = now()->addDays(10)->endOfDay();
        return response()->json(Appointment::with(['doctor','patient'])->where('doctor_id' , $id)->whereBetween('date', [$today, $endDate])->get()); 
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

    public function getMyAppointment() {
        
        $patient = Patient::firstWhere('user_id', auth()->id());
         //return response()->json(Appointment::with(['doctor','patient'])->where('patient_id' , $patient->id)->get()); 
        try{
         $appointments = Appointment::where('appointments.patient_id', $patient->id)
        ->join('doctors', 'appointments.doctor_id', '=', 'doctors.id')
        ->join('clinics', 'appointments.clinic_id', '=', 'clinics.id')
        ->select(
            'appointments.id',
            'appointments.doctor_id',
            'appointments.clinic_id',
            'appointments.date',
            'appointments.time',
            'appointments.status',
            'doctors.name as doctor_name',
            'clinics.title as clinic_name'
        )
        ->get();
        return response()->json($appointments , 200);
        }
        catch (\Exception $e) {
                return response()->json([
                    'message' => 'Server Error',
                    'error' => $e->getMessage()
                ], 500);
            }
    }



    public function store(Request $request) {
        $validated = $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'patient_id' => 'required|exists:patients,id',
            'clinic_id' => 'required|exists:clinics,id',
            'date' => 'required|date',
            'status' => 'required'
        ]);
        $appointmentDate = \Carbon\Carbon::parse($request->date);
        $today = now()->startOfDay();
        $endDate = now()->addDays(10)->endOfDay();

        if (!($appointmentDate >= $today->toDateString() && $appointmentDate <= $endDate->toDateString())) {
            return response()->json([
                "message" => "The appointment date must be between $today and $endDate ."
            ], 400);
        }

        $clinic = Clinic::findOrFail($request->clinic_id);

        $lastAppointment = Appointment::where('date', $request->date)
            ->where('clinic_id', $request->clinic_id)->orderBy('time', 'desc')->first();

        if ($lastAppointment) {
            $newTime = \Carbon\Carbon::createFromFormat('H:i:s', $lastAppointment->time)->addMinutes(30)->format('H:i:s');
        } else {
            $newTime = $clinic->start_time; 
        }
        if ($newTime >= $clinic->end_time) {
            return response()->json([
                'message' => 'No available slots, clinic is closed at this time.'
            ], 400);
        }

        $validated['time'] = $newTime;

        return response()->json(Appointment::create($validated), 201);
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
        $appointment = Appointment::with('patient')->findOrFail($id);
        if($appointment->patient->user_id !== auth()->id()){
            return response()->json(['message' => 'unAuthorized'] , 403);
        }
        Appointment::destroy($id);
        return response()->json(['message' => 'Deleted']);
    }
}

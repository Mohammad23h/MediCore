<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use App\Models\Doctor;
use Illuminate\Http\Request;

class ClinicController extends Controller
{
    public function index()
    {
        return response()->json(Clinic::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate(rules: [
            'title' => 'required|string',
            'logo_url' => 'logo_url|url',
            'start_day' => 'string',
            'end_day' => 'string',
            'start_time' => 'date_format:H:i',
            'end_time' => 'date_format:H:i',
            //'center_id' => 'required|exists:centers,id',
            'clinic_type' => 'required|string',
        ]);
        $validated['center_id'] = 1 ;
        return response()->json(Clinic::create($validated), 201);
    }

    public function show($id)
    {
        return response()->json(Clinic::with(['doctors'])->findOrFail(id: $id));
    }

    public function getDoctors($id)
    {
        return response()->json(Doctor::where('clinic_id',$id)->get());
    }

    public function update(Request $request, $id)
    {
        $clinic = Clinic::findOrFail($id);
        $Success = $clinic->update($request->all());
        if(!$Success){
            return response()->json(['message' => 'Failed'],400);
        }
        return response()->json($clinic);
    }

    public function addServices(Request $request, $clinicId)
    {
        /*
        $clinic = Clinic::findOrFail($id);
        $clinic->services()->attach($serviceId);
        $clinic = Clinic::with('services')->findOrFail($id);
        return response()->json($clinic);
        */

        $clinic = Clinic::findOrFail($clinicId);
        $validated = $request->validate([
            'services' => 'required|array',
            'services.*' => 'string',
        ]);

        // الخدمات الحالية
        $currentServices = $clinic->services ?? [];

        // دمج الخدمات الجديدة مع القديمة (بدون تكرار)
        $updatedServices = array_unique(array_merge($currentServices, $validated['services']));

        // تحديث العيادة
        $clinic->services = $updatedServices;
        $clinic->save();

        return response()->json([
            'message' => 'Services added successfully',
            'services' => $clinic->services,
        ], 200);

    }

    public function destroy($id)
    {
        Clinic::destroy($id);
        return response()->json(['message' => 'Deleted']);
    }
}

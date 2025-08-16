<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
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
            'center_id' => 'required|exists:centers,id',
            'clinic_type' => 'required|string',
        ]);

        return response()->json(Clinic::create($validated), 201);
    }

    public function show($id)
    {
        return response()->json(Clinic::with(['doctors'])->findOrFail(id: $id));
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

    public function destroy($id)
    {
        Clinic::destroy($id);
        return response()->json(['message' => 'Deleted']);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Laboratory;
use Illuminate\Http\Request;

class LaboratoryController extends Controller
{
    public function index()
    {
        return response()->json(Laboratory::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'logo_url'=>'nullable|string|url',
            'start_day'=> 'nullable|string',
            'end_day'=> 'nullable|string',
            'start_time'=> 'nullable|string',
            'end_time'=> 'nullable|string',
            'clinic_id'=> 'required|exists:clinics,id',
            'center_id' => 'required|exists:centers,id',
        ]);

        return response()->json(Laboratory::create($validated), 201);
    }

    public function show($id)
    {
        return response()->json(Laboratory::with(['center','technicians'])->findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $lab = Laboratory::with(['center','technicians'])->findOrFail($id);
        if($lab->center->user_id !== auth()->user()->id) {
            return response()->json(['message' => 'Access Denied'],403);
        }
        $Success = $lab->update($request->all());
        if(!$Success){
            return response()->json(['message' => 'Failed'],400);
        }
    }

    public function destroy($id)
    {
        Laboratory::destroy($id);
        return response()->json(['message' => 'Deleted']);
    }
}

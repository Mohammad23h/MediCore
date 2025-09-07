<?php

namespace App\Http\Controllers;

use App\Models\Laboratory;
use Illuminate\Http\Request;

class LaboratoryController extends Controller
{
    public function index()
    {
        return response()->json(Laboratory::with(['services'])->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'logo_url'=>'nullable|string|url',
            'start_day'=> 'required|string',
            'end_day'=> 'required|string',
            'start_time'=> 'nullable|string',
            'end_time'=> 'nullable|string',
            //'clinic_id'=> 'required|exists:clinics,id',
            'center_id' => 'required|exists:centers,id',
        ]);

        return response()->json(Laboratory::create($validated), 201);
    }

    public function show($id)
    {
        return response()->json(Laboratory::with(['services'])->findOrFail($id));
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
        return response()->json(['message' => 'Succeeded']);
    }

    public function destroy($id)
    {
        Laboratory::destroy($id);
        return response()->json(['message' => 'Deleted']);
    }


    public function addService(Request $request, $laboratoryId)
{
    $request->validate([
        'services' => 'required|array',
        'services.*' => 'exists:services,id'
    ]);

    $lab = Laboratory::findOrFail($laboratoryId);
    $lab->services()->attach($request->services);

    return response()->json([
        'laboratory' => $lab->load('services')
    ], 200);
}
}

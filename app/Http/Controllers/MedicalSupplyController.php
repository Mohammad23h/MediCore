<?php

namespace App\Http\Controllers;

use App\Models\MedicalSupply;
use Illuminate\Http\Request;

class MedicalSupplyController extends Controller
{
    public function index()
    {
        return response()->json(MedicalSupply::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'category' => 'required|string',
            'quantity' => 'required|integer|min:0',
            'reorder_level' => 'required|integer|min:0',
            'clinic_id' => 'nullable|exists:clinics,id',
            'center_id' => 'nullable|exists:centers,id',
        ]);

        return response()->json(MedicalSupply::create($validated), 201);
    }

    public function show($id)
    {
        
      $medical_supply = MedicalSupply::find($id);
        if (!$medical_supply) {
            return response()->json(['message' => 'this Medical_Supply is not found',], 404);
        }
        return response()->json($medical_supply);
        
    }

    public function update(Request $request, $id)
    {
        // $supply = MedicalSupply::findOrFail($id);
        // $supply->update($request->all());
        // return response()->json($supply);

          $medical_supply = MedicalSupply::findOrFail($id);
        /*if($medical_supply->center->user_id !== auth()->user()->id) {
            return response()->json(['message' => 'Access Denied'],403);
        }*/
        $Success = $medical_supply->update($request->all());
        if(!$Success){
            return response()->json(['message' => 'Failed'],400);
        }
        return response()->json($medical_supply , 200);
    }

    public function destroy($id)
    {
        MedicalSupply::destroy($id);
        return response()->json(['message' => 'Deleted']);
    }
}

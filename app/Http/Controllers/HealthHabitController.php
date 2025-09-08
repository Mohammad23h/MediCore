<?php

namespace App\Http\Controllers;

use App\Models\Health_Habit;
use Illuminate\Http\Request;

class HealthHabitController extends Controller
{
    //
    public function index()
    {
        return response()->json(Health_Habit::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required||exists:patients,id',
            'smoking' => 'required|boolean',
            'alcohol' => 'required|boolean',
            'diet' => 'nullable|max:255',
            'exercise' => 'nullable|max:255',
        ]);

        return response()->json(Health_Habit::create($validated), 201);
    }

    public function show($id)
    {
        return response()->json(Health_Habit::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $illness = Health_Habit::findOrFail($id);
        $illness->update($request->all());
        return response()->json($illness);
    }

    public function destroy($id)
    {
        Health_Habit::destroy($id);
        return response()->json(['message' => 'Deleted']);
    }
}

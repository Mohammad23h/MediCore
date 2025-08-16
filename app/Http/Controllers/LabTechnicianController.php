<?php

namespace App\Http\Controllers;

use App\Models\LabTechnician;
use Illuminate\Http\Request;

class LabTechnicianController extends Controller
{
    public function index()
    {
        return response()->json(LabTechnician::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:lab_technicians,email',
            'laboratory_id' => 'required|exists:laboratories,id',
        ]);

        return response()->json(LabTechnician::create($validated), 201);
    }

    public function show($id)
    {
        return response()->json(LabTechnician::with('laboratory')->findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $labTech = LabTechnician::findOrFail($id);
        $labTech->update($request->all());
        return response()->json($labTech);
    }

    public function destroy($id)
    {
        LabTechnician::destroy($id);
        return response()->json(['message' => 'Deleted']);
    }
}

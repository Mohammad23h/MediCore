<?php

namespace App\Http\Controllers;

use App\Models\XrayImages;
use Illuminate\Http\Request;

class XrayImagesController extends Controller
{
    //
      public function index()
    {
        return response()->json(XrayImages::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required||exists:patients,id',
            'file_path' => 'nullable|string',
            'description' => 'nullable|max:255',
            'uploaded_at' => 'nullable|date',
        ]);

        return response()->json(XrayImages::create($validated), 201);
    }

    public function show($id)
    {
        return response()->json(XrayImages::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $illness = XrayImages::findOrFail($id);
        $illness->update($request->all());
        return response()->json($illness);
    }

    public function destroy($id)
    {
        XrayImages::destroy($id);
        return response()->json(['message' => 'Deleted']);
    }
}

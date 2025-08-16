<?php

namespace App\Http\Controllers;

use App\Models\Illness;
use Illuminate\Http\Request;

class IllnessController extends Controller
{
    public function index()
    {
        return response()->json(Illness::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:illnesses,name',
            'description' => 'nullable|string',
        ]);

        return response()->json(Illness::create($validated), 201);
    }

    public function show($id)
    {
        return response()->json(Illness::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $illness = Illness::findOrFail($id);
        $illness->update($request->all());
        return response()->json($illness);
    }

    public function destroy($id)
    {
        Illness::destroy($id);
        return response()->json(['message' => 'Deleted']);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Assistant;
use Illuminate\Http\Request;

class AssistantController extends Controller
{
    public function index()
    {
        return response()->json(Assistant::all());
    }

    public function store(Request $request)
    {
      
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:assistants,email',
            'phone' => 'nullable|string',
            'center_id' => 'required|exists:centers,id',
        ]);
        $validated['user_id'] = auth()->id();

        return response()->json(Assistant::create($validated), 201);
    }

    public function show($id)
    {
        return response()->json(Assistant::with('center')->findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $assistant = Assistant::findOrFail($id);
        $assistant->update($request->all());
        return response()->json($assistant);
    }

    public function destroy($id)
    {
        Assistant::destroy($id);
        return response()->json(['message' => 'Deleted']);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Response;
use Illuminate\Http\Request;

class ResponseController extends Controller
{
    public function index()
    {
        return response()->json(Response::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'request_id' => 'required|exists:requests,id',
            'responder_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);

        return response()->json(Response::create($validated), 201);
    }

    public function show($id)
    {
        return response()->json(Response::with(['request', 'responder'])->findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $response = Response::findOrFail($id);
        $response->update($request->all());
        return response()->json($response);
    }

    public function destroy($id)
    {
        Response::destroy($id);
        return response()->json(['message' => 'Deleted']);
    }
}

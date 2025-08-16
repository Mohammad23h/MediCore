<?php

namespace App\Http\Controllers;

//use App\Models\Request;
use Illuminate\Http\Request;
use App\Models\Request as RequestModel;

class RequestController extends Controller
{
public function index()
    {
        return response()->json(RequestModel::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sender_id' => 'required|exists:users,id',
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);

        return response()->json(RequestModel::create($validated), 201);
    }

    public function show($id)
    {
        return response()->json(RequestModel::with(['sender', 'receiver'])->findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $req = RequestModel::findOrFail($id);
        $req->update($request->all());
        return response()->json($req);
    }

    public function destroy($id)
    {
        RequestModel::destroy($id);
        return response()->json(['message' => 'Deleted']);
    }
}

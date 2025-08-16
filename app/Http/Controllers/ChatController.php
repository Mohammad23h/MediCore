<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index()
    {
        return response()->json(Chat::with(['sender', 'receiver'])->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sender_id' => 'required|exists:users,id',
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);

        return response()->json(Chat::create($validated), 201);
    }

    public function show($id)
    {
        return response()->json(Chat::with(['sender', 'receiver'])->findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $chat = Chat::findOrFail($id);
        $chat->update($request->all());
        return response()->json($chat);
    }

    public function destroy($id)
    {
        Chat::destroy($id);
        return response()->json(['message' => 'Deleted']);
    }
}

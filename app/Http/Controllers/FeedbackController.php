<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function index()
    {
        return response()->json(Feedback::all());
    }

    public function GetAllReports()
    {
        return response()->json(Feedback::with('user')->Where('type','Report')->get());
    }

    public function GetAllSuggestions()
    {
        return response()->json(Feedback::with('user')->Where('type','Suggestion')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'date' => 'required|date',
            'type' => 'required|string|in:Suggestion,Report',
            'content' => 'required|string'
        ]);
        $validated['user_id'] = auth()->id();
        return response()->json(Feedback::create($validated), 201);
    }

    public function show($id)
    {
        return response()->json(Feedback::with('user')->findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $feedback = Feedback::findOrFail($id);
        $Success = $feedback->update($request->all());
        if(!$Success){
            return response()->json(['message' => 'Failed'],400);
        }
        return response()->json($feedback);
    }

    public function destroy($id)
    {
        Feedback::destroy($id);
        return response()->json(['message' => 'Deleted']);
    }
}

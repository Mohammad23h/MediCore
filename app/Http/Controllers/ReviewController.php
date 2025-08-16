<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index()
    {
        return response()->json(Review::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'content' => 'nullable|string',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        return response()->json(Review::create($validated), 201);
    }

    public function show($id)
    {
        return response()->json(Review::with(['patient', 'doctor'])->findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $review = Review::findOrFail($id);
        $review->update($request->all());
        return response()->json($review);
    }

    public function destroy($id)
    {
        Review::destroy($id);
        return response()->json(['message' => 'Deleted']);
    }
}

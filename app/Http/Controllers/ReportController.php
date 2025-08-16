<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        return response()->json(Report::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'content' => 'required|string',
            'type' => 'required|string',
        ]);

        return response()->json(Report::create($validated), 201);
    }

    public function show($id)
    {
        return response()->json(Report::with(['appointment'])->findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $report = Report::findOrFail($id);
        $report->update($request->all());
        return response()->json($report);
    }

    public function destroy($id)
    {
        Report::destroy($id);
        return response()->json(['message' => 'Deleted']);
    }
}

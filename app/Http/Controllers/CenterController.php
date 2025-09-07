<?php

namespace App\Http\Controllers;

use App\Models\Center;
use App\Traits\UploadImageTrait;
use Illuminate\Http\Request;

class CenterController extends Controller
{
    use UploadImageTrait;
    public function index()
    {
        return response()->json(Center::all()->makeHidden("user_id"));
    }

    public function store(Request $request)
    {
        $center = Center::firstWhere('user_id' , auth()->id());
        if($center){
            return response()->json(['message' => 'you have a center already'] , 400);
        }
        $validated = $request->validate([
            'name' => 'required|string',
            //'logo_url' => 'string|url',
            'start_day' => 'string',
            'end_day' => 'string',
            //'user_id' => 'required',
            'start_time' => 'date_format:H:i',
            'end_time' => 'date_format:H:i',
            'location' => 'required|string'
        ]);
        $validated['user_id'] = auth()->id();
        //$validated['logo_url'] = $this->UploadImage($request,'Centers');
        $imageUrl = null;
        if($request->hasFile('image')) {
            $file     = $request->file('image');
            $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('centers'), $fileName);

            $imageUrl = url('centers/' . $fileName);

        } else {
            $imageData = $request->input('image');

            if (strpos($imageData, 'base64,') !== false) {
                $imageData = explode('base64,', $imageData)[1];
            }

            $imageData = base64_decode($imageData);

            $fileName = uniqid() . '.png';
            $filePath = public_path('centers/' . $fileName);

            file_put_contents($filePath, $imageData);

            $imageUrl = url('centers/' . $fileName);
        }
        $validated['logo_url'] = $imageUrl;

        return response()->json(Center::create($validated), 201);
    }

    public function show($id)
    {
        $center = Center::with(['clinics.services', 'assistants'])->findOrFail($id);
        //$center->clinics->makeHidden('user_id');
        //$center->assistants->makeHidden('user_id');
        //$center->makeHidden('user_id');    
        return response()->json($center , 200);
    }

    public function showMyProfile() {
        $center = Center::With('clinics')->firstWhere('user_id',auth()->id());
        return response()->json($center); 
    }


public function updateImage(Request $request, $centerId)
{
    try{
    $center = Center::find($centerId);
    if(!$center) {
        return response()->json(['message' => 'Center not found'], 404);
    }

    $request->validate([
        'image' => 'required', // ملف أو base64
    ]);

    $imageUrl = null;

    // 1. تحديد المسار المستهدف وإنشاء المجلد إذا لم يكن موجوداً
    $targetDir = public_path('centers');
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    if($center->logo_url) {
        $oldPath = public_path(parse_url($center->logo_url, PHP_URL_PATH));
        if (file_exists($oldPath)) {
            @unlink($oldPath);
        }
    }

    if($request->hasFile('image')) {
        $file     = $request->file('image');
        $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($targetDir, $fileName); // استخدام المتغير $targetDir

        $imageUrl = url('centers/' . $fileName);

    } else {
        $imageData = $request->input('image');

        // تحقق إضافي من وجود البيانات
        if (!$imageData) {
            return response()->json(['message' => 'No image data provided'], 400);
        }

        if (strpos($imageData, 'base64,') !== false) {
            $imageData = explode('base64,', $imageData)[1];
        }

        $imageData = base64_decode($imageData);

        // تحقق إذا فشل فك التشفير
        if ($imageData === false) {
            return response()->json(['message' => 'Invalid base64 image data'], 400);
        }

        $fileName = uniqid() . '.png';
        $filePath = $targetDir . '/' . $fileName; // استخدام المتغير $targetDir

        file_put_contents($filePath, $imageData);

        $imageUrl = url('centers/' . $fileName);
    }

    $center->update(['logo_url' => $imageUrl]);

    return response()->json([
        'message'   => 'Center image updated successfully',
        'logo_url' => $imageUrl
    ], 200);
}catch (\Exception $e) {
                return response()->json([
                    'message' => 'Server Error',
                    'error' => $e->getMessage()
                ], 500);
            }
}


/*
    public function updateImage(Request $request, $centerId)
    {
        $center = Center::find($centerId);
        if(!$center) {
            return response()->json(['message' => 'Center not found'], 404);
        }

        $request->validate([
            'image' => 'required', // ملف أو base64
        ]);

        $imageUrl = null;

        if($center->image_url) {
            $oldPath = public_path(parse_url($center->image_url, PHP_URL_PATH));
            if (file_exists($oldPath)) {
                @unlink($oldPath);
            }
        }

        if($request->hasFile('image')) {
            $file     = $request->file('image');
            $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('centers'), $fileName);

            $imageUrl = url('centers/' . $fileName);

        } else {
            $imageData = $request->input('image');

            if (strpos($imageData, 'base64,') !== false) {
                $imageData = explode('base64,', $imageData)[1];
            }

            $imageData = base64_decode($imageData);

            $fileName = uniqid() . '.png';
            $filePath = public_path('centers/' . $fileName);

            file_put_contents($filePath, $imageData);

            $imageUrl = url('centers/' . $fileName);
        }

        $center->update(['logo_url' => $imageUrl]);

        return response()->json([
            'message'   => 'Center image updated successfully',
            'logo_url' => $imageUrl
        ], 200);
    }


*/


    public function update(Request $request, $id)
    {
        $center = Center::findOrFail($id);
        if($center->user_id !== auth()->user()->id) {
            return response()->json(['message' => 'Access Denied'],403);
        }
        $Success = $center->update($request->all());
        if(!$Success){
            return response()->json(['message' => 'Failed'],400);
        }
        return response()->json($center , 200);
    }

    public function destroy($id)
    {
        $center = Center::findOrFail($id);
        if($center->user_id !== auth()->user()->id) {
            return response()->json(['message' => 'Access Denied'],403);
        }
        Center::destroy($id);
        return response()->json(['message' => 'Deleted']);
    }
}

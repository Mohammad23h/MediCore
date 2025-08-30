<?php

namespace App\Traits;

use Illuminate\Http\Request;
trait UploadImageTrait{

    public function UploadImage(Request $request , $folderName){
        /*
        $image = $request->file('photo')->getClientOriginalName();
        $path = $request->file('photo')->storeAs($folderName , $image , 'mohammad');
        return $path;
        */

    if (!$request->hasFile('photo')) {
        return response()->json(['error' => 'No file uploaded'], 400);
    }

    $image = $request->file('photo')->getClientOriginalName();
    $path = $request->file('photo')->storeAs($folderName , $image , 'mohammad');

    $url = url('imgs/' . $path); // رابط مباشر للمتصفح
    return $url;


    }
}
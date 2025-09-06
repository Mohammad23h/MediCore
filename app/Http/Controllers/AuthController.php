<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

use Illuminate\Support\Facades\Mail;
class AuthController extends Controller
{
    /*
    public function login(Request $request)
    {
        // تحقق من صحة البيانات المدخلة
        $credentials = $request->only('email', 'password');

        // محاولة تسجيل الدخول
        if (!$token = Auth::attempt($credentials)) {
            return response()->json(['error' => 'بيانات الدخول غير صحيحة'], 401);
        }

        // نجاح: أرجع التوكن وبعض المعلومات
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            //'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }
        */

    public function getMyUser(Request $request){
        return response()->json([
            'user' => auth()->user(),
        ]);
    }
    
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'=> 'required|email',
            'password'=> 'required|string|min:6',
        ]);
        if ($validator->fails()){
            return response()->json($validator->errors(), 422);
        }
        if (! $token = auth()->attempt($validator->validated())) {
            return response()->json(['error' => 'unauthurized'], 401);
        }

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            //'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
        
    }

    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|email|string|max:100|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'string'
        ]);
        if ($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        $user = User::create(array_merge(
            $validator->validated() ,
             ['password' => bcrypt($request->get('password'))],
        ));

        if (! $token = auth()->attempt([
            'email' => $request->email,
            'password' => $request->password,
        ])) {
            return response()->json(['error' => 'unauthurized'], 401);
        }
        
        //$token = $user->createToken('api-token')->plainTextToken;
        return response()->json([
            'message' => 'Your account has registered sussessful',
            'User' => $user,
            'access_token' => $token, 
        ]);
    }


    public function destroy($id){
        User::destroy($id);
        return response()->json(['message' => 'Deleted']);
    }

    
    public function registerWithVerify(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|email|string|max:100|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'string'
        ]);
        if ($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        $user = User::create(array_merge(
            $validator->validated() ,
             ['password' => bcrypt($request->get('password'))],
        ));
        $code = mt_rand(100000, 999999);

        $user->verification_code = $code;
        $user->verification_code_expires_at = Carbon::now()->addMinutes(10);
        $user->save();
        
        // إرسال البريد
        Mail::raw("Your verification code is: $code", function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('verification code');
        });

        return response()->json(['message' => 'Verifivation code has been sent' ]);

/*
        return response()->json([
            'message' => 'Your account has registered sussessful',
            'User' => $user,
        ]);*/
    }

    public function verifyEmail(Request $request){
        $request->validate([
        'email' => 'required|email',
        'code'  => 'required|string',
    ]);

    $user = User::where('email', $request->email)->first();

    if (! $user) {
        return response()->json(['error' => 'user is not existed'], 404);
    }

    if ($user->verification_code !== $request->code) {
        return response()->json(['error' => 'error in code'], 400);
    }

    if (Carbon::now()->greaterThan($user->verification_code_expires_at)) {
        return response()->json(['error' => 'your verify code had expired'], 400);
    }

    $user->email_verified_at = Carbon::now();
    $user->verification_code = null;
    $user->verification_code_expires_at = null;
    $user->save();
    $token = $user->createToken('api-token')->plainTextToken;
    return response()->json(['message' => 'تم تفعيل البريد الإلكتروني بنجاح ✅' , "token" => $token ]);
    }

    public function getRole(Request $request)
    {
        $user = auth()->user();

        return response()->json([
            'role' => $user->role,
        ]);
    }

    public function block($id){
        /*
        if(auth()->user()->role != 'admin'){
            return response()->json(['message' => 'unauthorize'] , 403);
        }
            */
        $user = User::findOrFail($id);
        $user->blocked = true;
        $user->save();
        return response()->json(['blocked_user' => $user], 200);
    }

    public function unBlock($id){
        /*
        if(auth()->user()->role != 'admin'){
            return response()->json(['message' => 'unauthorize'] , 403);
        }
            */
        $user = User::findOrFail($id);
        $user->blocked = false;
        $user->save();
        return response()->json(['unblocked_user' => $user], 200);
    }

    public function forgetPassword(Request $request)
    {
        /*
        $request->validate(['email' => 'required|email']);

        Mail::raw('Hello', function ($message) use ($request) {
        $message->to($request->email)
                ->subject('Hello');
                
        
    });

    return response()->json(['message' => 'sent successfully']);
    */
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return response()->json(['error' => ' not found the user'], 404);
        }

        $resetCode = rand(100000, 999999);

        $user->reset_code = $resetCode;
        $user->reset_code_expires_at = Carbon::now()->addMinutes(15);
        $user->save();

        Mail::raw("Your reset code is :{$resetCode}", function ($message) use ($user) {
        $message->to($user->email)
                ->subject('reset code');
        });

        return response()->json(['message' => 'reset code has been sent successfuly']);
    }

    public function verifyResetCode(Request $request){
        $request->validate([
            'email' => 'required|email',
            'code'  => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return response()->json(['error' => 'not found the user'], 404);
        }

        if ($user->reset_code !== $request->code) {
            return response()->json(['error' => 'error in your code'], 400);
        }

        if (Carbon::now()->greaterThan($user->reset_code_expires_at)) {
            return response()->json(['error' => 'code is expired'], 400);
        }

        return response()->json(['message' => 'Succeeded']);
    }

    public function resetPassword(Request $request){
        $request->validate([
        'email' => 'required|email',
        'code' => 'required|string',
        'new_password' => 'required|string|min:8',
    ]);

    $user = User::where('email', $request->email)->first();

    if (! $user) {
        return response()->json(['error' => 'المستخدم غير موجود'], 404);
    }

    if ($user->reset_code !== $request->code) {
        return response()->json(['error' => 'user not found'], 400);
    }

    if (Carbon::now()->greaterThan($user->reset_code_expires_at)) {
        return response()->json(['error' => 'code has expired'], 400);
    }

    $user->password = Hash::make($request->new_password);

    $user->reset_code = null;
    $user->reset_code_expires_at = null;

    $user->save();

    return response()->json(['message' => 'succeeded']);
    }
    public function getAllUsers(Request $request)
    {
        $users = User::all();

        return response()->json($users);
    }

    public function giveRole(Request $request)
    {
        $user = User::findOrFail($request["user_id"]);
        $user->role = $request["role"];

        return response()->json([
            'role' => $user->role,
            'user' => $user
        ]);
    }


}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;
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

        return response()->json([
            'message' => 'Your account has registered sussessful',
            'User' => $user,
        ]);
    }

    public function getRole(Request $request)
    {
        $user = auth()->user();

        return response()->json([
            'role' => $user->role,
        ]);
    }
/*
    public function forgetPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => __($status)])
            : response()->json(['error' => __($status)], 400);
    }
*/
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

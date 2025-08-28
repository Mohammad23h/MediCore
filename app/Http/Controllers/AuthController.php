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
        return response()->json([
            'message' => 'Your account has registered sussessful',
            'User' => $user,
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


        // توليد كود عشوائي مكون من 6 أرقام
        $code = mt_rand(100000, 999999);

        // حفظ الكود وتاريخ الانتهاء (مثلاً 10 دقائق من الآن)
        $user->verification_code = $code;
        $user->verification_code_expires_at = Carbon::now()->addMinutes(10);
        $user->save();
        
        // إرسال البريد
        Mail::raw("كود التفعيل الخاص بك هو: $code", function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('كود تفعيل البريد الإلكتروني');
        });

        return response()->json(['message' => 'تم إرسال كود التفعيل بنجاح' ]);

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
        return response()->json(['error' => 'المستخدم غير موجود'], 404);
    }

    if ($user->verification_code !== $request->code) {
        return response()->json(['error' => 'الكود غير صحيح'], 400);
    }

    if (Carbon::now()->greaterThan($user->verification_code_expires_at)) {
        return response()->json(['error' => 'انتهت صلاحية الكود'], 400);
    }

    // تفعيل الحساب
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

    public function forgetPassword(Request $request)
    {
        /*
        $request->validate(['email' => 'required|email']);

        Mail::raw('مرحبا', function ($message) use ($request) {
        $message->to($request->email)
                ->subject('اختبار الإيميل');
                
        
    });

    return response()->json(['message' => 'تم إرسال البريد بنجاح']);
    */
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return response()->json(['error' => 'المستخدم غير موجود'], 404);
        }

        // توليد كود من 6 أرقام
        $resetCode = rand(100000, 999999);

        // تخزين الكود ووقت الانتهاء (15 دقيقة من الآن)
        $user->reset_code = $resetCode;
        $user->reset_code_expires_at = Carbon::now()->addMinutes(15);
        $user->save();

        // إرسال الإيميل
        Mail::raw("رمز استعادة كلمة المرور الخاص بك هو: {$resetCode}", function ($message) use ($user) {
        $message->to($user->email)
                ->subject('رمز استعادة كلمة المرور');
        });

        return response()->json(['message' => 'تم إرسال رمز الاستعادة إلى بريدك الإلكتروني']);
    }

    public function verifyResetCode(Request $request){
        $request->validate([
            'email' => 'required|email',
            'code'  => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return response()->json(['error' => 'المستخدم غير موجود'], 404);
        }

        if ($user->reset_code !== $request->code) {
            return response()->json(['error' => 'الكود غير صحيح'], 400);
        }

        if (Carbon::now()->greaterThan($user->reset_code_expires_at)) {
            return response()->json(['error' => 'انتهت صلاحية الكود'], 400);
        }

        return response()->json(['message' => 'تم التحقق من الكود بنجاح']);
    }

    public function resetPassword(Request $request){
        $request->validate([
        'email'        => 'required|email',
        'code'         => 'required|string',
        'new_password' => 'required|string|min:8',
    ]);

    $user = User::where('email', $request->email)->first();

    if (! $user) {
        return response()->json(['error' => 'المستخدم غير موجود'], 404);
    }

    if ($user->reset_code !== $request->code) {
        return response()->json(['error' => 'الكود غير صحيح'], 400);
    }

    if (Carbon::now()->greaterThan($user->reset_code_expires_at)) {
        return response()->json(['error' => 'انتهت صلاحية الكود'], 400);
    }

    // تحديث كلمة المرور
    $user->password = Hash::make($request->new_password);

    // مسح الكود بعد الاستخدام
    $user->reset_code = null;
    $user->reset_code_expires_at = null;

    $user->save();

    return response()->json(['message' => 'تمت إعادة تعيين كلمة المرور بنجاح']);
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

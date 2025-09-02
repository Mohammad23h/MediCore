<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LabTechnicianController;
use App\Http\Controllers\MedicalSupplyController;
use App\Http\Controllers\PatientIllnessController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\ResponseController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\IllnessController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\CenterController;
use App\Http\Controllers\ClinicController;
use App\Http\Controllers\MedicalRecordController;
use App\Http\Controllers\LabTestController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\LaboratoryController;
use App\Http\Controllers\AssistantController;
use Illuminate\Support\Facades\Password;
//use App\Http\Controllers\UserController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/reset-password/{token}', function ($token) {
    return "هنا المفروض تعرض صفحة reset password بـ Frontend مع التوكن: $token";
})->name('password.reset');
Route::post('doctors/upload', [DoctorController::class, 'upload']);


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('users/login', [AuthController::class,'login']);
Route::post('users/register', [AuthController::class,'register']);
Route::post('users/register-with-verify', [AuthController::class,'registerWithVerify']);
Route::post('users/verify-email', [AuthController::class,'verifyEmail']);
Route::get('users', [AuthController::class,'getAllUsers']);
Route::get('users/myUser', [AuthController::class,'getMyUser']);
Route::post('users/forget', [AuthController::class,'forgetPassword']);
Route::post('users/verify-reset-code', [AuthController::class,'verifyResetCode']);
Route::post('users/reset-password', [AuthController::class,'resetPassword']);
/*Route::post('/forgot-password', function (Request $request) {
    $request->validate(['email' => 'required|email']);

    $status = Password::sendResetLink(
        $request->only('email')
    );

    return $status === Password::RESET_LINK_SENT
        ? response()->json(['message' => __($status)])
        : response()->json(['error' => __($status)], 400);
});*/
Route::post('/forgot-password', function (Request $request) {
    $request->validate(['email' => 'required|email']);

    Mail::raw('مرحبا', function ($message) use ($request) {
        $message->to($request->email)
                ->subject('اختبار الإيميل');
    });

    return response()->json(['message' => 'تم إرسال البريد بنجاح']);
});
Route::middleware('auth:api')->get('/user-role', [AuthController::class, 'getRole']);
Route::put('users/role', [AuthController::class,'giveRole']);
Route::put('users/block/{id}', [AuthController::class,'block']);


Route::controller(LabTechnicianController::class)->prefix('lab-technicians')->group(function () {
    Route::get('/', 'index');
    Route::post('/', 'store');
    Route::get('{id}', 'show');
    Route::put('{id}', 'update');
    Route::delete('{id}', 'destroy');
});

Route::controller(MedicalSupplyController::class)->prefix('medical-supplies')->group(function () {
    Route::get('/', 'index');
    Route::post('/', 'store');
    Route::get('{id}', 'show');
    Route::put('{id}', 'update');
    Route::delete('{id}', 'destroy');
});

Route::controller(PatientIllnessController::class)->prefix('patient-illnesses')->group(function () {
    Route::get('/', 'index');
    Route::post('/', 'store');
    Route::get('{id}', 'show');
    Route::put('{id}', 'update');
    Route::delete('{id}', 'destroy');
});

Route::controller(RequestController::class)->prefix('requests')->group(function () {
    Route::get('/', 'index');
    Route::post('/', 'store');
    Route::get('{id}', 'show');
    Route::put('{id}', 'update');
    Route::delete('{id}', 'destroy');
});

Route::controller(ResponseController::class)->prefix('responses')->group(function () {
    Route::get('/', 'index');
    Route::post('/', 'store');
    Route::get('{id}', 'show');
    Route::put('{id}', 'update');
    Route::delete('{id}', 'destroy');
});

Route::controller(ReviewController::class)->prefix('reviews')->group(function () {
    Route::get('/', 'index');
    Route::post('/', 'store');
    Route::get('{id}', 'show');
    Route::put('{id}', 'update');
    Route::delete('{id}', 'destroy');
});

Route::controller(IllnessController::class)->prefix('illnesses')->group(function () {
    Route::get('/', 'index');
    Route::post('/', 'store');
    Route::get('{id}', 'show');
    Route::put('{id}', 'update');
    Route::delete('{id}', 'destroy');
});

Route::controller(FeedbackController::class)->prefix('feedbacks')->group(function () {
    Route::get('/', 'index')->middleware(['auth:api', 'inRoles:doctor,center,assistant']);
    Route::get('/suggestions', 'GetAllSuggestions')->middleware(['auth:api', 'inRoles:center']);
    Route::get('/reports', 'GetAllReports')->middleware(['auth:api', 'inRoles:center']);
    Route::post('/', 'store')->middleware(['auth:api']);
    Route::get('{id}', 'show')->middleware(['auth:api', 'inRoles:doctor,center,assistant']);
    Route::put('{id}', 'update');
    Route::delete('{id}', 'destroy');
});

Route::controller(PatientController::class)->prefix('patients')->group(function () {
    Route::get('/', 'index');//->middleware(['auth:api', 'inRoles:doctor,center,assistant']);
    Route::get('myProfile', 'showMyProfile')->middleware(['auth:api', 'inRoles:patient']);
    Route::post('/', 'store')->middleware(['auth:api', 'inRoles:patient']);
    Route::get('{id}', 'show')->middleware(['auth:api', 'inRoles:doctor,center,assistant']);
    Route::put('{id}', 'update')->middleware(['auth:api', 'inRoles:patient']);
    Route::delete('{id}', 'destroy');
});

Route::controller(DoctorController::class)->prefix('doctors')->group(function () {
    Route::get('/', 'index');
    Route::post('/', 'store')->middleware(['auth:api', 'doctor']);
    Route::post('upload', 'upload');
    Route::put('add-to-clinic', 'addToClinic')->middleware(['auth:api', 'center']);
    Route::get('myProfile', 'showMyProfile')->middleware(['auth:api', 'doctor']);
    Route::get('{id}', 'show');
    Route::put('{id}', 'update')->middleware(['auth:api', 'doctor']);
    Route::delete('{id}', 'destroy')->middleware(['auth:api', 'center']);
    Route::put('block/{id}', 'block');//->middleware(['auth:api', 'doctor']);
    
    //Route::delete('{id}', 'destroyMyProfile')->middleware(['auth:api', 'doctor']);
    
});

Route::controller(AppointmentController::class)->prefix('appointments')->group(function () {
    Route::get('/', 'index');
    Route::post('/', 'store');
    Route::get('{id}', 'show');
    Route::get('getByDate/{id}', 'getDoctorAppointmentInDate');
    Route::put('{id}', 'update');
    Route::delete('{id}', 'destroy');
});

Route::controller(CenterController::class)->prefix('centers')->group(function () {
    Route::get('/', 'index');
    Route::post('/', 'store')->middleware(['auth:api', 'center']);
    Route::get('myProfile', 'showMyProfile')->middleware(['auth:api', 'center']);
    Route::get('{id}', 'show');
    Route::put('{id}', 'update')->middleware(['auth:api', 'center']);
    Route::delete('{id}', 'destroy')->middleware(['auth:api', 'center']);
});

Route::controller(ClinicController::class)->prefix('clinics')->group(function () {
    Route::get('/', 'index');
    Route::post('/', 'store')->middleware(['auth:api', 'center']);
    Route::get('{id}', 'show');
    Route::get('{id}/doctors', 'getDoctors');
    Route::post('{id}/addServices', 'addServices');
    Route::put('{id}', 'update')->middleware(['auth:api', 'center']);
    Route::delete('{id}', 'destroy');
});


Route::controller(MedicalRecordController::class)->prefix('medical-records')->group(function () {
    Route::get('/', 'index')->middleware(['auth:api', 'inRoles:doctor,assistant,center']);
    Route::post('/', 'store')->middleware(['auth:api', 'inRoles:assistant']);
    Route::get('/search', 'searchByPatientName')->middleware(['auth:api', 'inRoles:assistant,doctor']);
    Route::get('{id}', 'show')->middleware(['auth:api', 'inRoles:assistant']);
    Route::put('{id}', 'update')->middleware(['auth:api', 'inRoles:assistant']);
    Route::delete('{id}', 'destroy')->middleware(['auth:api', 'inRoles:assistant']);
});

Route::controller(LabTestController::class)->prefix('lab-tests')->group(function () {    
    Route::get('/', 'index')->middleware(['auth:api', 'inRoles:assistant,doctor']);
    Route::post('/', 'store')->middleware(['auth:api', 'inRoles:assistant,doctor']);
    Route::get('/search', 'searchByPatientName')->middleware(['auth:api', 'inRoles:assistant,doctor']);
    Route::get('{id}', 'show')->middleware(['auth:api', 'inRoles:assistant']);
    Route::put('{id}', 'update');
    Route::delete('{id}', 'destroy')->middleware(['auth:api', 'inRoles:assistant']);   
});

Route::controller(ReportController::class)->prefix('reports')->group(function () {
    Route::get('/', 'index');
    Route::post('/', 'store');
    Route::get('{id}', 'show');
    Route::put('{id}', 'update');
    Route::delete('{id}', 'destroy');
});

Route::controller(ChatController::class)->prefix('chats')->group(function () {
    Route::get('/', 'index');
    Route::post('/', 'store');
    Route::get('{id}', 'show');
    Route::put('{id}', 'update');
    Route::delete('{id}', 'destroy');
});

Route::controller(ServiceController::class)->prefix('services')->group(function () {
    Route::get('/', 'index');
    Route::post('/', 'store');
    Route::get('{id}', 'show');
    Route::put('{id}', 'update');
    Route::delete('{id}', 'destroy');
});


Route::controller(LaboratoryController::class)->prefix('laboratories')->group(function () {
    Route::get('/', 'index')->middleware(['auth:api', 'center']);;
    Route::post('/', 'store')->middleware(['auth:api', 'center']);
    Route::get('{id}', 'show')->middleware(['auth:api', 'center']);;
    Route::put('{id}', 'update')->middleware(['auth:api', 'center']);
    Route::delete('{id}', 'destroy')->middleware(['auth:api', 'center']);
});


Route::controller(AssistantController::class)->prefix('assistants')->group(function () {
    Route::get('/', 'index')->middleware(['auth:api', 'inRoles:doctor,assistant']);
    Route::post('/', 'store')->middleware(['auth:api', 'inRoles:assistant']);
    Route::get('{id}', 'show');
    Route::put('{id}', 'update');
    Route::delete('{id}', 'destroy');
});
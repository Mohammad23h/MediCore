<?php
namespace App\Http\Controllers;

use App\Events\NewMessage;
use App\Models\Center;
use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\Request as RequestModel;
use App\Models\Response;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Mail\Events\MessageSent;

class ChatController extends Controller
{
    // فتح محادثة جديدة بين المريض والمركز
    public function startChat() {
        try {
            
            $patient = Patient::firstWhere('user_id',auth()->id());
            $chat = Chat::firstOrCreate([
                'patient_id' => $patient->id,
                'center_id' => 1
            ]);
        return response()->json($chat, 201);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Server Error',
            'error' => $e->getMessage()
        ], 500);
    }
    }

    // إرسال ريكويست من المريض
    public function sendRequest(Request $request) {
        try{
            $patient = Patient::firstWhere('user_id',auth()->id());
            $chat = Chat::firstWhere('patient_id',$patient->id);
            $requestModel = RequestModel::create([
                'chat_id' => $chat->id,
                'patient_id' => $patient->id,
                'content' => $request->content,
                'sent_at' => now(),
            ]);

            // يمكنك إطلاق Event لبث الرسالة مباشرة عبر Pusher
            broadcast(new NewMessage($chat->id, $requestModel))->toOthers();
            //event(new MessageSent($requestModel));
            return response()->json($requestModel, 201);
            }
            catch (\Exception $e) {
                return response()->json([
                    'message' => 'Server Error',
                    'error' => $e->getMessage()
                ], 500);
            }
        
    }

    // إرسال response من المركز
    public function sendResponse(Request $request) {
        $center = Center::firstWhere('user_id',auth()->id());
        $chat = Chat::firstWhere('center_id',$center->id);
        $response = Response::create([
            'chat_id' => $chat->id,
            'center_id' => $center->id,
            'content' => $request->content,
            'sent_at' => now(),
        ]);

         broadcast(new NewMessage($chat->id, $response))->toOthers();
        //event(new MessageSent($response));

        return response()->json($response, 201);
    }

    // جلب المحادثة مع جميع الرسائل
    public function getChat($chatId) {
        $chat = Chat::with(['requests','responses'])->findOrFail($chatId);
        return response()->json($chat);
    }
}

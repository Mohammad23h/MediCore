<?php
namespace App\Http\Controllers;

use App\Events\NewMessage;
use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\Request as RequestModel;
use App\Models\Response;
use Carbon\Carbon;

class ChatController extends Controller
{
    // فتح محادثة جديدة بين المريض والمركز
    public function startChat() {
        $chat = Chat::firstOrCreate([
            'patient_id' => auth()->id(),
            'center_id' => 1
        ]);
        return response()->json($chat, 201);
    }

    // إرسال ريكويست من المريض
    public function sendRequest(Request $request, $chatId) {
        $requestModel = RequestModel::create([
            'chat_id' => $chatId,
            'patient_id' => auth()->id(),
            'content' => $request->content,
            'sent_at' => Carbon::now(),
        ]);

        // يمكنك إطلاق Event لبث الرسالة مباشرة عبر Pusher
         broadcast(new NewMessage($chatId, $requestModel))->toOthers();

        return response()->json($requestModel, 201);
    }

    // إرسال response من المركز
    public function sendResponse(Request $request, $chatId) {
        $response = Response::create([
            'chat_id' => $chatId,
            'center_id' => auth()->id(),
            'content' => $request->content,
            'sent_at' => Carbon::now(),
        ]);

         broadcast(new NewMessage($chatId, $response))->toOthers();

        return response()->json($response, 201);
    }

    // جلب المحادثة مع جميع الرسائل
    public function getChat($chatId) {
        $chat = Chat::with(['requests','responses'])->findOrFail($chatId);
        return response()->json($chat);
    }
}

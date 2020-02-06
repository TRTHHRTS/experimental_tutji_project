<?php
namespace App\Http\Controllers;

use App\Mailable\NewMessageMail;
use App\Models\Message;
use App\Models\User;
use App\Models\UserDetails;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class MessagesController extends Controller
{
    public function getUserMessages(){
        $user = Auth::user();
        $userId = $user->id;

        if ($user) {
            $result = [];
            $messages = DB::select('
            SELECT * FROM (
              SELECT M.sender_id, M.rcpt_id, M.message, M.readed, M.created_at,
                @n:=if(@lid=if(sender_id='.$userId.',rcpt_id,sender_id),@n+1,1) N,
                @lid:=if(sender_id='.$userId.',rcpt_id,sender_id)
            FROM messages M,(select @lid:=NULL,@n:=0) A
            WHERE '.$userId.' IN (sender_id,rcpt_id)
            ORDER BY if(sender_id='.$userId.',rcpt_id,sender_id),created_at desc ) A
            WHERE N=1;');
            foreach ($messages as $value) {
                $message = [];
                $message['date'] = $value->created_at;
                $message['text'] = $value->message;
                $rcptId = $value->sender_id != $userId ? $value->sender_id : $value->rcpt_id;
                $message['photo_url'] = UserDetails::where('user_id', $rcptId)->first()->photo_url;
                $message['username'] = User::find($rcptId)->name;
                $message['rcpt_id'] = $rcptId;
                array_push($result, $message);
            }
            return response($result);
        }
        return response([]);
    }

    public function getChannelMessages($rcptId) {
        $user = Auth::user();
        $userId = $user->id;
        $messages = Message::where([['sender_id', $userId], ['rcpt_id', $rcptId], ['hidden', false]])->
            orWhere([['sender_id', $rcptId], ['rcpt_id', $userId], ['hidden', false]])->oldest()->get(['message', 'created_at', 'sender_id', 'hidden']);
        foreach ($messages as $value) {
            $value['from_you'] = $value->sender_id == $userId ? true : false;
            unset($value->rcpt_id);
            unset($value->hidden);
        }
        $result = [
            'messages' => $messages,
            'username' => User::find($rcptId)->name,
            'user_id' => $rcptId,
        ];
        return response($result);
    }

    public function sendMessage(Request $request) {
        if (!$request->has("rcptId") || !$request->has("text")) {
            return response(__("errors.incorrectRequest"), 422);
        }
        $rcpt = User::find($request->get("rcptId"));

        $mes = new Message();
        $mes->sender_id = Auth::id();
        $mes->rcpt_id = $rcpt->id;
        $mes->message = $request->get("text");
        $mes->save();

        // Уведомляем при необходимости получателя
        if (!$rcpt->isOnline() && $rcpt->email && $rcpt->email_verified && $rcpt->settings->notify_new_messages) {
            $mail = new NewMessageMail($rcpt->name, Auth::user()->name, Carbon::now()->toDateTimeString(), $mes->message);
            Mail::to($rcpt->email)->send($mail);
        }
        return response(['status'=>0]);
    }

    public function createChannel() {
        $userId = Auth::id();

        $data = $_POST;
        $rcptId = $data['RCPT'];

        $messageCount = Message::where([['sender_id', $userId], ['rcpt_id', $rcptId]])->orWhere([['sender_id', $rcptId], ['rcpt_id', $userId]])->count();

        if ($messageCount == 0) {
            $mes = new Message();
            $mes->sender_id = $userId;
            $mes->rcpt_id = $rcptId;
            $mes->hidden = true;
            $mes->message = '';
            $mes->save();
        }

        return response($rcptId);
    }
}

<?php

namespace App\Http\Controllers\Api\Message;

use App\Http\Controllers\Api\Interaction\NotificationController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Message\MessageStoreRequest;
use App\Models\Message;
use App\Models\User;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;

class MessageStoreController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(MessageStoreRequest $request)
    {
        try {

            $receiver = User::find($request->receiver_id);

            if($receiver && !$receiver->creator->id && !auth()->user()->creator->id){
                return response()->json([
                    'status' => 'error',
                    'message' => 'Les Messages entre utilisateurs simples ne sont pas encore possibles.',
                ], 403);
            }  
            
            if($request->type === 'image' && $request->hasFile('image')){
                $content = $request->file('image')->store('messages', 'public');
            }else if($request->type === 'text'  && $request->text){
                $contentBrut = $request->text;
                $contentTrimed = trim($contentBrut);
                $content = strip_tags($contentTrimed);
            }

            $receiver_id = ($receiver)?  $request->receiver_id : 0;

            $message = Message::create([
                'content' => $content,
                'type' => $request->type,
                'receiver_id' => $receiver_id,
                'sender_id' => auth()->user()->id
            ]);

            // push notification for receiver user
            $notificationContent = ($message->type == 'image') ? "Une image vous a été envoyé." : $content;

            $notificationData  = [
                'title' => "Un nouveau message",
                'content' => $notificationContent,
                'user_id' => $receiver_id,
                'notifiable_id' => $message->id,
                'notifiable_type' => \App\Models\Message::class,
            ];

            (new NotificationController)->store($notificationData);

            return response()->json([
                'status' => 'success',
                'message' => 'Message stockée avec succès.',
                'data' => $message
            ], 201);

        }catch (Exception $e) {
            return response()->json($e);
        }
    }
}

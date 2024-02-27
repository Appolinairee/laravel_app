<?php

namespace App\Http\Controllers\Api\Message;

use App\Http\Controllers\Controller;
use App\Http\Requests\Message\MessageStoreRequest;
use App\Models\Message;
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
            
            if($request->type === 'image' && $request->hasFile('image')){
                $content = $request->file('image')->store('messages', 'public');
            }else if($request->type === 'text'  && $request->text){
                $contentBrut = $request->text;
                $contentTrimed = trim($contentBrut);
                $content = strip_tags($contentBrut);
            }

            $message = Message::create([
                'content' => $content,
                'type' => $request->type,
                'receiver_type' => $request->receiver_type,
                'receiver_id' => $request->receiver_id,
                'sender_id' => auth()->user()->id
            ]);

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

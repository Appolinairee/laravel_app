<?php

namespace App\Http\Controllers\Api\Message;

use App\Http\Controllers\Controller;
use App\Http\Requests\Message\MessageUpdateRequest;
use App\Models\Message;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;

class MessageUpdateController extends Controller
{
    /**
     * Handle the incoming request for user register.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

     public function __invoke(MessageUpdateRequest $request, Message $message){
        try {
            $this->authorize('updateMessage', $message);
            
            if($message->type !== 'text'){
                return response()->json([
                    'status' => 'error',
                    'message' => 'Le message ne peut être mise à jour.',
                ], 403);
            }

            $message->update([
                "content" => $request->text,
                "status" => $request->status
            ]);

            return response()->json([
                'status' => 'success',
                'message' => "L'unité de commande est mise à jour.",
                'data' =>$message
            ], 200);

        }catch (AuthorizationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Vous n\'êtes pas autorisé à faire cette action.',
            ], 403);

        }   catch (Exception $e) {
            return response()->json($e);
        }
    }
}

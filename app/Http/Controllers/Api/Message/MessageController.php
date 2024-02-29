<?php

namespace App\Http\Controllers\Api\Message;

use App\Http\Controllers\Controller;
use App\Models\Creator;
use App\Models\Message;
use App\Models\User;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * Handle the store media request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

     public function messagesByUser(User $user, Request $request){
        try {

            if(auth()->user()->creator || auth()->user()->isAdmin){
                $perPage = $request->get('perPage', 10);
            
                $messages = auth()->user()->messages()
                    ->where('receiver_id', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->paginate($perPage);


                return response()->json([
                    'status' => 'success',
                    'message' => 'Messages de l\'utilisateur.',
                    'data' => $messages->items(),
                    'pagination' => [
                        'nextUrl' => $messages->nextPageUrl(),
                        'prevUrl' => $messages->previousPageUrl(),
                        'total' => $messages->total(),
                    ],
                ], 201);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Vous n\'êtes pas autorisé à faire cette action.',
            ], 403);

        }catch (Exception $e) {
            return response()->json($e);
        }
    }


    /**
     * Handle the store media request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

     public function messagesByCreator(Creator $creator, Request $request){
        try {
            $perPage = $request->get('perPage', 10);

            $messages = $creator->messages()
                ->where('sender_id', auth()->user()->id)
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);


            return response()->json([
                'status' => 'success',
                'message' => 'Messages de l\'utilisateur.',
                'data' => $messages->items(),
                'pagination' => [
                    'nextUrl' => $messages->nextPageUrl(),
                    'prevUrl' => $messages->previousPageUrl(),
                    'total' => $messages->total(),
                ],
            ], 201);        
            

        }catch (Exception $e) {
            return response()->json($e);
        }
    }


    /**
     * Handle the store media request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function delete(Message $message){
        try {
            
            $this->authorize('updateMessage', $message);
                
                // solf delete
            $message->delete();
                
            return response()->json([
                'status' => 'success',
                'message' => 'Message supprimé.'
            ], 200);

        }catch (AuthorizationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Vous n\'êtes pas autorisé à faire cette action.',
            ], 403);

        }  catch (Exception $e) {
            return response()->json($e);
        }
    }



}

<?php

namespace App\Http\Controllers\Api\Message;

use App\Http\Controllers\Controller;
use App\Models\Message;
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

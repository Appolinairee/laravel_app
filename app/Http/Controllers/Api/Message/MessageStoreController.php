<?php

namespace App\Http\Controllers\Api\Message;

use App\Http\Controllers\Controller;
use App\Http\Requests\Message\MessageStoreRequest;
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
            
            $message = new Message([
                'type' => $validatedData['type'],
                'receiver_type' => $validatedData['receiver_type'],
                'receiver_id' => $validatedData['receiver_id'],
            ]);


        }catch (Exception $e) {
            return response()->json($e);
        }
    }
}

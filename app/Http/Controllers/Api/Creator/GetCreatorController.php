<?php

namespace App\Http\Controllers\Api\Creator;

use App\Http\Controllers\Controller;
use App\Models\Creator;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;

class GetCreatorController extends Contr
oller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke($creatorId)
    {
        try {
            $creator = Creator::find($creatorId);

            if(($creator && auth()->user()->id == $creator->user_id) || auth()->user()->isAdmin()){

                if($creator){
                    return response()->json([
                        'status' => 'success',
                        'data' => $creator,
                    ], 201);
                }else{
                    return response()->json([
                        'status' => 'error',
                        'message' => 'L\'utilisateur n\'est pas créateur.',
                    ], 404);
                }
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Vous n\'êtes pas autorisé à faire cette action.',
            ], 403);


        } catch (Exception $e) {
            return response()->json($e);
        }
    }
}
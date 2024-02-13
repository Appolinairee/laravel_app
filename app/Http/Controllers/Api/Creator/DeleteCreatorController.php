<?php

namespace App\Http\Controllers\Api\creator;

use App\Http\Controllers\Controller;
use App\Models\Creator;
use Exception;
use Illuminate\Http\Request;

class DeleteCreatorController extends Controller
{
    public function __invoke(Creator $creator){

        try {
            if (auth()->user()->id !== $creator->user_id && !auth()->user()->isAdmin()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Vous n\'avez pas l\'autorisation de mettre à jour ce créateur.',
                ], 403);
            }else{

                // solf delete
                $creator->delete();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Suppression effectuée avec succès.'
                ], 200);
            }

        } catch (Exception $e) {
            return response()->json($e);
        }
    }
}

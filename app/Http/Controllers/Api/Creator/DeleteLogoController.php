<?php

namespace App\Http\Controllers\Api\Creator;

use App\Http\Controllers\Controller;
use App\Models\Creator;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DeleteLogoController extends Controller
{
    public function __invoke(Creator $creator){
        try {
            if(auth()->user()->id == $creator->user_id || auth()->user()->isAdmin()){

                //delete old logo
                Storage::delete($creator->logo);

                $creator->update(['logo' => null]);

                return response()->json([
                    'success' => true,
                    'message' => 'Le média a été supprimé avec succès.'
                ], 200);

            }else{
                return response()->json([
                    'status' => 'error',
                    'message' => 'Vous n\'avez pas l\'autorisation de mettre à jour ce créateur.',
                ], 403);
            }
        } catch (Exception $e) {
            return response()->json($e);
        }
    }
}

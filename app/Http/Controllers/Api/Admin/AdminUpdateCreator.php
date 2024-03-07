<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Creator;
use Exception;
use Illuminate\Http\Request;

class AdminUpdateCreator extends Controller
{

    /**
     * This method updates the status of a creator to activate it.
     *
     * @param  \App\Models\Creator  $creator
     * @return \Illuminate\Http\JsonResponse
     */

    public function activeCreator(Creator $creator)
    {
        try {

            $creator->update([
                "status" => 1
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Le produit est activé avec succès.',
                'data' =>  $creator
            ], 200);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }
}

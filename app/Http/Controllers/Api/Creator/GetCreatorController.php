<?php

namespace App\Http\Controllers\Api\Creator;

use App\Http\Controllers\Controller;
use App\Models\Creator;
use App\Models\Product;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;

class GetCreatorController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getCreator($creatorId)
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

    public function getCreators(Request $request)
    {
        try {
            $perPage = $request->input('perPage', 15);
            $query = $request->input('query');
            $creatorsQuery = Creator::where('status', 1);

            if ($query) {
                $creatorsQuery->where('name', 'like', '%' . $query . '%');
            }

            $creatorsData = $creatorsQuery->paginate($perPage);

            return response()->json([
                'status' => 'success',
                'current_page' => $creatorsData->currentPage(),
                'data' => $creatorsData->items(),
                'nextUrl' => $creatorsData->nextPageUrl(),
                'prevUrl' => $creatorsData->previousPageUrl(),
                'total' => $creatorsData->total(),
            ], 200); 

        } catch (Exception $e) {
            return response()->json($e);
        }
    }
}
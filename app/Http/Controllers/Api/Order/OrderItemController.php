<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use Exception;
use Illuminate\Http\Request;

class OrderItemController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function orderItem(OrderItem $orderItem){
        try {
            
            if((auth()->user()->id == $orderItem->order->user->id) || auth()->user()->isAdmin() || auth()->user()->id == $orderItem->product->creator_id){

                return response()->json([
                        'status' => 'success',
                        'data' => $orderItem->makeHidden(['order', 'product']),
                ], 201);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Vous n\'êtes pas autorisé à faire cette action.',
            ], 403);


        } catch (Exception $e) {
            return response()->json($e);
        }
    }


    public function delete(OrderItem $orderItem){

        try {
            if ((auth()->user()->id !== $orderItem->order->user->id) && !auth()->user()->isAdmin() && auth()->user()->id !== $orderItem->product->creator_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Vous n\'avez pas l\'autorisation de mettre à jour ce créateur.',
                ], 403);
            }else{

                // solf delete
                $orderItem->delete();

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

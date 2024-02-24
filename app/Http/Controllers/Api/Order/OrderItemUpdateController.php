<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\OrderItemUpdateRequest;
use App\Models\Order;
use App\Models\OrderItem;
use Exception;
use Illuminate\Http\Request;

class OrderItemUpdateController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(OrderItem $orderItem, OrderItemUpdateRequest $request)
    {
        try {

            if (auth()->user()->id !== $orderItem->order->user->id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Vous n\'avez pas l\'autorisation de mettre à jour ce créateur.',
                ], 403);
            }

            $orderItemData = $request->only(['quantity', 'status', 'order_id']);


            if (empty($orderItemData)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Aucune information à mettre à jour.',
                ], 400);
            }


            // when product number is set by creator and under request quantity
            if($orderItem->product->quantity && $orderItem->product->quantity < $request->quantity){
                return response()->json([
                    'status' => 'error',
                    'message' => 'Le produit n\'est pas disponible pour cette quantité.',
                ], 403);
            }

            $orderItem->update($orderItemData);

            return response()->json([
                'status' => 'success',
                'message' => "L'unité de commande est mise à jour.",
                'data' => $orderItem->makeHidden(['order', 'product'])
            ], 200);

        } catch (Exception $e) {
            return response()->json($e);
        }
    }
}

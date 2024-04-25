<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\OrderItemUpdateRequest;
use App\Models\OrderItem;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;

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
            $this->authorize('updateOrderItem', $orderItem->order);

            $orderItemData = $request->only(['quantity', 'status', 'order_id']);

            if (empty($orderItemData)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Aucune information à mettre à jour.',
                ], 400);
            }

            // when product quantity is set by creator and under request quantity
            if($orderItem->product->quantity && $orderItem->product->quantity < $request->quantity){
                return response()->json([
                    'status' => 'error',
                    'message' => 'Le produit n\'est pas disponible pour cette quantité.',
                ], 403);
            }

            // when order status is -1 (when order is under payment and receive its first transaction)
            if($orderItem->order->status > 1 || $orderItem->order->amount_paid > 0 ){
                return response()->json([
                    'status' => 'error',
                    'message' => 'Impossible de mettre à jour la commande d\'appartenance. Paiement en cours.',
                ], 403);
            }

            $orderItem->update($orderItemData);

            return response()->json([
                'status' => 'success',
                'message' => "L'unité de commande est mise à jour.",
                'data' => $orderItem->makeHidden(['order', 'product'])
            ], 200);

        } catch (AuthorizationException $e) {

            return response()->json([
                'status' => 'error',
                'message' => 'Vous n\'êtes pas autorisé à faire cette action.',
            ], 403);

        } catch (Exception $e) {
            return response()->json($e);
        }
    }
}

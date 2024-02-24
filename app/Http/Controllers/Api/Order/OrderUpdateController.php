<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\OrderUpdateRequest;
use App\Models\Order;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OrderUpdateController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(OrderUpdateRequest $request, Order $order){
        try {
            $this->authorize('update', $order);

            if ($request->user()->can('updateOrderItem', $order)) {
                $orderData = $request->only(['status', 'shipping_address']);
            }

            if($request->user()->can('updateShipping', $order)){
                $orderData = $request->only(['status', 'shipping_price', 'shipping_service', 'shipping_date']);
            }


            if (empty($orderData) && $request->shipping_preview) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Aucune information à mettre à jour.',
                ], 400);
            }

            //store preview image
            if($request->hasFile('shipping_preview')){
                //delete old preview image
                if($order->shipping_preview && Storage::disk('public')->exists($order->shipping_preview)){
                    Storage::disk('public')->delete($order->shipping_preview);
                }

                // store new logo
                $previewPath = $request->file('shipping_preview')->store('delievering', 'public');
                $order->update(['shipping_preview' => $previewPath]);
            }

            $order->update($orderData);

            return response()->json([
                'status' => 'success',
                'message' => "L'unité de commande est mise à jour.",
                'data' => $order
            ], 200);

        }catch (AuthorizationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Vous n\'êtes pas autorisé à faire cette action.',
            ], 403);

        }   catch (Exception $e) {
            return response()->json($e);
        }
    }
}

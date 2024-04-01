<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\OrderItemStoreRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;

class OrderItemStoreController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(OrderItemStoreRequest $request)
    {
        try {
            $product = Product::find($request->product_id);

            $order =  $product->orderItems()->whereHas('product', function ($query) use ($product) {
                $query->where('creator_id', $product->creator_id);
            })->first()->order()->get();

            if (!$order) {
                $order = Order::create([
                    'user_id' => auth()->user()->id,
                    'creator_id' => $product->creator_id
                ]);

                $message = "Création d'une nouvelle commande. Produit ajouté à la commande.";
            } else {
                $this->authorize('storeInExistingOrder', $order);
                $message = "Produit ajouté à la commande";
            }

            // when order status is -1 (when order is under payment and receive its first transaction)
            if ($order->status > 1 || $order->amount_paid > 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Impossible de mettre à jour la commande d\'appartenance. Paiement en cours.',
                ], 403);
            }


            // when product already exists in order
            if ($order->order_items()->where('product_id', $product->id)->exists()) {
                return response()->json([
                    'status' => 'false',
                    'message' => 'Ce produit existe dans la commande.',
                ], 403);
            }


            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $request->quantity
            ]);

            $totalAmount = $order->calculateTotalAmount();
            $order->load('order_items');

            return response()->json([
                'status' => 'success',
                'message' => $message,
                'data' => $order,
                'totalAmount' => $totalAmount,
            ], 201);
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

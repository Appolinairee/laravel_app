<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\OrderItemStoreRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;

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
            if(!$request->order_id){
                $order = Order::create([
                    'user_id' => auth()->user()->id,
                ]);

                $message = "Création d'une nouvelle commande. Produit ajouté à la commande.";

            }else{
                $order = Order::findOrFail($request->order_id);
                $message = "Produit ajouté à la commande";
            }

            $product = Product::find($request->product_id);


            // when product already exists in order
            if($order->order_items()->where('product_id', $product->id)->exists()){
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

            return response()->json([
                'status' => 'success',
                'message' => $message,
                'data' => Order::with('order_items')->where('id', $order->id)->orderBy('created_at', 'desc')->first(),
                'totalAmount' => $totalAmount,
            ], 201);

        } catch (Exception $e) {
            return response()->json($e);
        }
    }

}

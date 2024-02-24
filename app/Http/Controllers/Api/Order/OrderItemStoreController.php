<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\OrderItemStoreRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
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
            $product = Product::find($request->product_id);

            if(!$request->order_id){
                $order = Order::create([
                    'user_id' => auth()->user()->id,
                    'creator_id' => $product->creator_id
                ]);

                $message = "Création d'une nouvelle commande. Produit ajouté à la commande.";
            }else{
                
                $order = Order::findOrFail($request->order_id);

                if($product->creator_id !== $order->creator_id){
                    $order = Order::create([
                        'user_id' => auth()->user()->id,
                        'creator_id' => $product->creator_id
                    ]);
    
                    $message = "Le produit est pour un créateur diffférent. Il a été ajouté à une nouvelle commande.";
                }else{
                    $this->authorize('storeInExistingOrder', $order);
                    $message = "Produit ajouté à la commande";
                }

            }


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

        } catch (AuthorizationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Vous n\'êtes pas autorisé à faire cette action.',
            ], 403);
            
        }   catch (Exception $e) {
            return response()->json($e);
        }
    }

}

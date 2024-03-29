<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use App\Models\Creator;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class OrderGetController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function getOrder($orderId)
    {
        try {
            $order = Order::with('order_items', 'creator')->findOrFail($orderId);

            $this->authorize('getOrder', $order);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'order' => $order,
                    'contributions' => $order->contributions
                ]
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




    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function ordersByUser(User $user, Request $request)
    {
        try {
            if (auth()->user()->id === $user->id || auth()->user()->isAdmin()) {
                $perPage = $request->get('perPage', 10);

                $orders = $user->orders()
                    ->orderBy('created_at', 'desc')
                    ->paginate($perPage);

                return response()->json([
                    'status' => 'success',
                    'current_page' => $orders->currentPage(),
                    'data' => $orders->items(),
                    'pagination' => [
                        'nextUrl' => $orders->nextPageUrl(),
                        'prevUrl' => $orders->previousPageUrl(),
                        'total' => $orders->total(),
                    ],
                ], 200);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Vous n\'êtes pas autorisé à faire cette action.',
            ], 403);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }




    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function ordersByCreator(Creator $creator, Request $request)
    {
        try {
            if (auth()->user()->id === $creator->user_id || auth()->user()->isAdmin()) {
                $perPage = $request->get('perPage', 10);

                $orders = $creator->orders()
                    ->orderBy('created_at', 'desc')
                    ->paginate($perPage);

                return response()->json([
                    'status' => 'success',
                    'current_page' => $orders->currentPage(),
                    'data' => $orders->items(),
                    'pagination' => [
                        'nextUrl' => $orders->nextPageUrl(),
                        'prevUrl' => $orders->previousPageUrl(),
                        'total' => $orders->total(),
                    ],
                ], 200);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Vous n\'êtes pas autorisé à faire cette action.',
            ], 403);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }



    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function ordersByProduct(Product $product, Request $request)
    {
        try {
            if ((auth()->user()->creator && auth()->user()->creator->id  === $product->creator_id) || auth()->user()->isAdmin()) {
                $perPage = $request->get('perPage', 10);


                $orders = Order::whereHas('order_items', function ($query) use ($product) {
                    $query->where('product_id', $product->id);
                })->with('order_items')->paginate($perPage);

                return response()->json([
                    'status' => 'success',
                    'current_page' => $orders->currentPage(),
                    'data' => $orders->items(),
                    'pagination' => [
                        'nextUrl' => $orders->nextPageUrl(),
                        'prevUrl' => $orders->previousPageUrl(),
                        'total' => $orders->total(),
                    ],
                ], 200);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Vous n\'êtes pas autorisé à faire cette action.',
            ], 403);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }


    public function delete(Order $order){

        try {
            $this->authorize('delete', $order);

            // solf delete
            $order->delete();

            return response()->json([
                    'status' => 'success',
                    'message' => 'Suppression effectuée avec succès.'
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

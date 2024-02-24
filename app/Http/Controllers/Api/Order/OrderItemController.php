<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;

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
            $this->authorize('getOrder', $orderItem->order);

            return response()->json([
                'status' => 'success',
                'data' => $orderItem->makeHidden(['order', 'product']),
            ], 201);

        } catch (AuthorizationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Vous n\'êtes pas autorisé à faire cette action.',
            ], 403);
        }        
        catch (Exception $e) {
            return response()->json($e);
        }
    }


    public function delete(OrderItem $orderItem){

        try {
            $this->authorize('delete', $orderItem->order);

            // solf delete
            $orderItem->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Suppression effectuée avec succès.'
            ], 200);

        } catch(AuthorizationException $e){
            return response()->json([
                'status' => 'error',
                'message' => 'Vous n\'êtes pas autorisé à faire cette action.',
            ], 403);
        }catch (Exception $e) {
            return response()->json($e);
        }
    }


}

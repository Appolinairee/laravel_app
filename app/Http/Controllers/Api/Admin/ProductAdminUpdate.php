<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;

class ProductAdminUpdate extends Controller
{

    /**
     * This method updates the status of a product to activate it.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\JsonResponse
     */

    public function activeProduct(Product $product)
    {
        try {

            $newStatus = $product->status == 1 ? 0 : 1;

            $product->update([
                "status" => $newStatus
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Le produit est activé avec succès.',
                'data' =>  $product
            ], 200);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }
}

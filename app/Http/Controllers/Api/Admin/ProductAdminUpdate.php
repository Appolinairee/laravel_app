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

            $product->update([
                "status" => 1
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

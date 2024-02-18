<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;

class UpdateProductController extends Controller
{
    public function __invoke(Product $product, UpdateProductRequest $request){
        try {

            if (auth()->user()->id !== $product->creator_id && !auth()->user()->isAdmin()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Vous n\'avez pas l\'autorisation de mettre à jour ce créateur.',
                ], 403);
            }else{
                $productData = $request->only(['title', 'caracteristics', 'delivering', 'old_price', 'current_price']);


                if (empty($productData)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Aucune information à mettre à jour.',
                    ], 400);
                }

                $product->update($productData);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Mise à jour du produit effectuée.',
                    'data' => $product,
                ], 200);
            }

        } catch (Exception $e) {
            return response()->json($e);
        }
    }
}

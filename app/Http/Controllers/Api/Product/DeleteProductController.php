<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;

class DeleteProductController extends Controller
{
    public function __invoke(Product $product){
        try {
            if (auth()->user()->id !== $product->creator_id && !$this->authorize('isAdmin', auth()->user())) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Vous n\'avez pas l\'autorisation de supprimer cet utilisateur.',
                ], 403);
            } else{
                
                // solf delete
                $product->delete();
                
                return response()->json([
                    'status' => 'success',
                    'message' => 'Suppression du produit effectuée avec succès.'
                ], 200);
            }

        } catch (Exception $e) {
            return response()->json($e);
        }
    }
}

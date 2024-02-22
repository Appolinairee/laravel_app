<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use Exception;
use Illuminate\Support\Str;
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
                $productData = $request->only(['title', 'caracteristics', 'delivering', 'old_price', 'current_price', 'disponibility']);


                if (empty($productData) && empty($request->category_ids) && $request->new_category) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Aucune information à mettre à jour.',
                    ], 400);
                }


                // quantity can be update when disponibility is 1(true)
                if(isset($request->quantity) && $request->disponibility == 1){
                    $productData['quantity'] = $request->quantity;
                }else if(isset($request->quantity) && $request->disponibility != 1){
                    return response()->json([
                        'status' => 'false',
                        'message' => 'Produit non disponible. Vous ne pouvez indiquer le nombre',
                    ], 403);
                }


                $product->update($productData);

                if(!empty($request->category_ids)){
                    $product->categories()->detach();
                    $product->categories()->attach($request->category_ids);
                }

                if($request->new_category){
                    $slug = Str::slug($request->new_category);
    
                    $categorie = Category::create([
                        'name' => $request->new_category,
                        'image' => null,
                        'slug' => $slug,
                        'statut' => 'inactive'
                    ]);
                }

                return response()->json([
                    'status' => 'success',
                    'message' => 'Mise à jour du produit effectuée.',
                    'data' =>  $product->fresh('categories')
                ], 200);
            }

        } catch (Exception $e) {
            return response()->json($e);
        }
    }
}
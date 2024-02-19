<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class GetProductController extends Controller
{
    public function getProducts(Request $request){
        try {
            $perPage = $request->get('perPage', 15);
            $page = $request->get('page', 1);
            $query = $request->get('query');


            if($query){
                $productsQuery = Product::query();
                $allProducts = $productsQuery->where('title', 'like', '%' . $query . '%')->get();
                // dd($allProducts->get());
            }else{
                $allProducts = Product::with('medias')->get();
            }
        

            $selectedProducts = collect();

            $productsPerGroup = 1;
            $cycles = ceil($allProducts->count() / ($productsPerGroup * 3));

            for ($cycle=0; $cycle < $cycles; $cycle++) { 

                //random products
                $randomProducts = $allProducts
                    ->reject(function ($product) use ($selectedProducts) {
                        return $selectedProducts->contains('id', $product->id);
                    })
                    ->shuffle()
                    ->take($productsPerGroup);
                
                $selectedProducts = $selectedProducts->merge($randomProducts);
                
                if ($randomProducts->count() < $productsPerGroup) {
                    break;
                }
                
                // latests products
                $latestProducts = $allProducts
                    ->reject(function ($product) use ($selectedProducts) {
                        return $selectedProducts->contains('id', $product->id);
                    })
                    ->sortByDesc('created_at')
                    ->take($productsPerGroup);
                
                $selectedProducts = $selectedProducts->merge($latestProducts);
                
                if ($latestProducts->count() < $productsPerGroup) {
                    break;
                }

                //popular products
                $popularProducts = $allProducts
                    ->reject(function ($product) use ($selectedProducts) {
                        return $selectedProducts->contains('id', $product->id);
                    })
                    ->sortByDesc('created_at')
                    ->take($productsPerGroup);
                
                $selectedProducts = $selectedProducts->merge($popularProducts);
                
                if ($popularProducts->count() < $productsPerGroup) {
                    break;
                }

            }

            $productsArray = $selectedProducts->toArray();

            $offset = ($page - 1) * $perPage;
            $paginatedProducts = array_slice($productsArray, $offset, $perPage);
            $totalProducts = count($productsArray);
            $paginator = new LengthAwarePaginator($paginatedProducts, $totalProducts, $perPage, $page);


            return response()->json([
                'status' => 'success',
                'data' => $paginator->items(),
                'pagination' => [
                    'current_page' => $paginator->currentPage(),
                    'last_page' => $paginator->lastPage(),
                    'per_page' => $paginator->perPage(),
                    'total' => $paginator->total(),
                    'prev_page_url' => $paginator->previousPageUrl(),
                    'next_page_url' => $paginator->nextPageUrl(),
                ],
            ], 200);


        }catch (Exception $e) {
            return response()->json($e);
        }
    }


}
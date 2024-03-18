<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Creator;
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

            $productsArray = $selectedProducts->map(function ($product) {
                $product->likes_count = $product->likes()->count();
                
                $product->comments_count = $product->comments()->count();
                return $product;
            })->toArray();


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


        } catch (Exception $e) {
            return response()->json($e);
        }
    }


    public function getProductsPresentations () {
        try {
            $products = $products = Product::with('medias')->where('status', 2)->get()->shuffle();

            return response()->json([
                'status' => 'success',
                'data' => $products,
            ], 200);

        } catch (Exception $e) {
            return response()->json($e);
        }
    }



    public function getProduct($productId){
        try {
            $product = Product::with('medias', 'categories')->findOrFail($productId);

            $product->similarProducts = $product->similarProducts();

            return response()->json([
                'status' => 'success',
                'data' => $product
            ], 200);


        }catch (Exception $e) {
            return response()->json($e);
        }
    }
    
    
    public function getProductByCreator(Creator $creator, Request $request){
        try {
            
            $perPage = $request->get('perPage', 15);

            $products = $creator->products()
                        ->orderBy('created_at', 'desc')
                        ->paginate($perPage);

            return response()->json([
                'status' => 'success',
                'current_page' => $products->currentPage(),
                'data' => $products->items(),
                'pagination' => [
                    'nextUrl' => $products->nextPageUrl(),
                    'prevUrl' => $products->previousPageUrl(),
                    'total' => $products->total(),
                ],
            ], 200); 

        }catch (Exception $e) {
            return response()->json($e);
        }
    }




    public function getProductByCategory(Category $category, Request $request){
        try {

            $perPage = $request->get('perPage', 15);

            $products = $category->products()
                        ->orderBy('created_at', 'desc')
                        ->paginate($perPage);

            return response()->json([
                'status' => 'success',
                'current_page' => $products->currentPage(),
                'data' => $products->items(),
                'pagination' => [
                    'nextUrl' => $products->nextPageUrl(),
                    'prevUrl' => $products->previousPageUrl(),
                    'total' => $products->total(),
                ],
            ], 200); 

        }catch (Exception $e) {
            return response()->json($e);
        }
    }


    public function getProductsInTrash(Request $request){
        try {
            $perPage = $request->get('perPage', 15);

            $products = Product::onlyTrashed()->paginate($perPage);
        
            return response()->json([
                'status' => 'success',
                'current_page' => $products->currentPage(),
                'data' => $products->items(),
                'pagination' => [
                    'nextUrl' => $products->nextPageUrl(),
                    'prevUrl' => $products->previousPageUrl(),
                    'total' => $products->total(),
                ],
            ], 200); 

        }catch (Exception $e) {
            return response()->json($e);
        }
    }
    
}

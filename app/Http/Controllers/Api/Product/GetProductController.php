<?php

namespace App\Http\Controllers\Api\Product;

use App\Helpers\FrontendLink;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Creator;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class GetProductController extends Controller
{
    public function getProducts(Request $request)
    {
        try {
            $perPage = $request->get('perPage', 6);
            $page = $request->get('page', 1);
            $queryParam = $request->get('query');
            $userId = intval($request->get('user_id'));

            $productsQuery = Product::query()
                ->where('status', 1)
                ->with(['creator' => function ($query) {
                    $query->select('id', 'name', 'logo');
                }])->select('id', 'title', 'old_price', 'current_price', 'creator_id', 'disponibility', 'slug_name');
             
            if ($queryParam) {
                $productsQuery->where(function ($query) use ($queryParam) {
                    $query->where('title', 'like', '%' . $queryParam . '%')
                        ->orWhere('caracteristics', 'like', '%' . $queryParam . '%');
                });
            }

            $selectedProducts = collect();

            $latestProducts = $productsQuery->latest()->take($perPage)->get();
            $selectedProducts = $selectedProducts->merge($latestProducts);

            $randomProducts = $productsQuery->inRandomOrder()->take($perPage)->get();
            $selectedProducts = $selectedProducts->merge($randomProducts);

            $popularProducts = $productsQuery->withCount('likes')->orderByDesc('likes_count')->take($perPage)->get();
            $selectedProducts = $selectedProducts->merge($popularProducts);


            $paginatedProducts = $productsQuery->paginate($perPage);

            foreach ($paginatedProducts as $product) {
                $this->getProductOtherDetails($product, $userId);
                $product->can_comment = $product->canComment($userId);
            }

            $paginatedProducts->appends([
                'perPage' => $perPage,
                'page' => $page,
                'query' => $queryParam,
                'user_id' => $userId,
            ]);

            return response()->json([
                'status' => 'success',
                'current_page' => $paginatedProducts->currentPage(),
                'data' => $paginatedProducts->items(),
                'pagination' => [
                    'nextUrl' => $paginatedProducts->nextPageUrl(),
                    'prevUrl' => $paginatedProducts->previousPageUrl(),
                    'total' => $paginatedProducts->total(),
                ],
            ], 200);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    public function getProductsPresentations()
    {
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

    private function getProductOtherDetails($product, $userId)
    {
        $product->likes_count = $product->likes()->count();
        $product->comments_count = $product->comments()->count();
        $product->medias_count = $product->medias()->count();
        $product->is_liked = $product->isLiked($userId);

        $product->load(['medias' => function ($query) {
            $query->take(1);
        }]);

        if ($userId) {
            $affiliateCode = User::findOrFail($userId)->affiliate_code;
            $product->affiliation_link = (new FrontendLink())->affiliateLink($product->title, $affiliateCode);
        }
    }


    public function getProduct($productSlug, Request $request)
    {
        try {
            $userId = $request->get('user_id');
            $product = Product::with(['medias', 'creator', 'comments' => function ($query) {
                $query->latest();
            }])->where('slug_name', $productSlug)->where('status', 1)->first();

            
            if ($product) {
                // $product->similarProducts = $product->similarProducts();
                $product->likes_count = $product->likes()->count();
                $product->comments_count = $product->comments()->count();
                $product->medias_count = $product->medias()->count();
                $product->is_liked = $product->isLiked($userId);
                $product->can_comment = $product->canComment($userId);
            }

            if ($userId) {
                $affiliateCode = User::findOrFail($userId)->affiliate_code;
                $product->affiliation_link = (new FrontendLink())->affiliateLink($product->title, $affiliateCode);
                $product->is_liked = $product->isLiked($userId);
            }

            foreach ($product->comments as $comment) {
                $carbonDate = Carbon::parse($comment->updated_at);
                $comment->time_ago = $carbonDate->diffForHumans();
                $comment->user_name = $comment->user()->select('name')->value('name');
            }

            return response()->json([
                'status' => 'success',
                'data' => $product
            ], 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function getProductByCreator(Request $request, Creator $creator)
    {
        try {
            $perPage = $request->get('perPage', 15);
            $products = $creator->products()->where('status', 1)->orderBy('created_at', 'desc')->paginate($perPage);
            $userId = $request->get('user_id');

            foreach ($products as $product) {
                $this->getProductOtherDetails($product, $userId);
                $product->is_liked = $product->isLiked($userId);
                $product->load('medias');
            }

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
        } catch (Exception $e) {
            return response()->json($e);
        }
    }


    public function getProductByCategory(Category $category, Request $request)
    {
        try {
            $perPage = $request->get('perPage', 15);
            $userId = $request->get('user_id');

            $products = $category->products()->where('status', 1)
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            foreach ($products as $product) {
                $this->getProductOtherDetails($product, $userId);
                $product->is_liked = $product->isLiked($userId);
            }

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
        } catch (Exception $e) {
            return response()->json($e);
        }
    }


    public function getProductsInTrash(Request $request)
    {
        try {
            $perPage = $request->get('perPage', 15);
            $userId = $request->get('user_id');

            $products = Product::onlyTrashed()->paginate($perPage);

            foreach ($products as $product) {
                $this->getProductOtherDetails($product, $userId);
                $product->is_liked = $product->isLiked($userId);
            }

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
        } catch (Exception $e) {
            return response()->json($e);
        }
    }


    public function getToValidateProducts(Request $request)
    {
        try {
            $perPage = $request->get('perPage', 15);
            $userId = $request->get('user_id');

            $products = Product::where('status', 0)->orderBy('created_at', 'desc')
                ->paginate($perPage);

            foreach ($products as $product) {
                $this->getProductOtherDetails($product, $userId);
            }

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
        } catch (Exception $e) {
            return response()->json($e);
        }
    }


    public function getToValidateProduct($productId)
    {
        try {
            $product = Product::with('medias', 'categories')->where('status', 0)->findOrFail($productId);

            return response()->json([
                'status' => 'success',
                'data' => $product
            ], 200);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }
}

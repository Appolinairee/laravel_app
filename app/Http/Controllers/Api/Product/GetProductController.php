<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class GetProductController extends Controller
{
    public function getProducts(){
        try {

            $products = Product::all();

            return response()->json([
                'status' => 'success',
                'data' => $products,
            ], 201);

        }catch (Exception $e) {
            return response()->json($e);
        }
    }


}

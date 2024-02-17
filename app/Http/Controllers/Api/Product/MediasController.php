<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreMediaRequest;
use App\Http\Requests\Product\StoreProductImageRequest;
use App\Models\Product;
use Illuminate\Http\Request;

class MediasController extends Controller
{
    
     /**
     * Handle the store media request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function storeImage(Product $product, StoreProductImageRequest $request){
        dd($request, $product);
    }
}

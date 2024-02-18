<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreMediaRequest;
use App\Http\Requests\Product\StoreProductImageRequest;
use App\Http\Requests\Product\StoreProductVideoRequest;
use App\Models\Media;
use App\Models\Product;
use Exception;
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

        try {
            if($product->creator_id == auth()->user()->id || auth()->user()->isAdmin()){

                $imageCount = $product->medias()->where('type', 'image')->count();

                if($imageCount >= 5){
                    return response()->json([
                        'success' => false,
                        'message' => 'Le produit a déjà atteint le nombre maximal d\'images (5).',
                    ], 422);
                }
                    
                $imagePath = $request->file('image')->store('products/image', 'public');

                $media = Media::create([
                    'link' => $imagePath,
                    'type' => 'image',
                    'product_id' => $product->id,
                ]);

                return response()->json([
                    'status' => true,
                    'message' => 'L\'image a été ajoutée avec succès au produit.',
                    'data' => $media
                ], 201);
            
            }else{

                return response()->json([
                    'status' => 'false',
                    'message' => 'Vous n\'avez pas l\'autorisation d\'ajouter cette image.',
                ], 403);
            }
            
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    public function storeVideo(Product $product, StoreProductVideoRequest $request){

        try {
            if($product->creator_id == auth()->user()->id || auth()->user()->isAdmin()){

                $videoCount = $product->medias()->where('type', 'video')->count();

                if($videoCount >= 2){
                    return response()->json([
                        'success' => false,
                        'message' => 'Le produit a déjà atteint le nombre maximal de vidéo (2).',
                    ], 422);
                }
                
                $videoPath = $request->file('video')->store('products/video', 'public');

                $media = Media::create([
                    'link' => $videoPath,
                    'type' => 'video',
                    'product_id' => $product->id,
                ]);

                return response()->json([
                    'status' => true,
                    'message' => 'La vidéo a été ajoutée avec succès au produit.',
                    'data' => $media
                ], 201);
            
            }else{

                return response()->json([
                    'status' => 'false',
                    'message' => 'Vous n\'avez pas l\'autorisation d\'ajouter cette vidéo.',
                ], 403);
            }
            
        } catch (Exception $e) {
            return response()->json($e);
        }
    }


    public function delete(Product $product, Media $media){
        try {
            if($product->creator_id == auth()->user()->id || auth()->user()->isAdmin()){
                $media->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Le média a été supprimé avec succès.',
                ], 200);
            }else{

                return response()->json([
                    'status' => 'false',
                    'message' => 'Vous n\'avez pas l\'autorisation d\'ajouter cette vidéo.',
                ], 403);
            }
            
        } catch (Exception $e) {
            return response()->json($e);
        }
    }
}

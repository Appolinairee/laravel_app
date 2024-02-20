<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\CategoriesStoreRequest;
use App\Http\Requests\Product\CategoriesUpdateRequest;
use App\Models\Category;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoriesController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CategoriesStoreRequest $request)
    {
        try {

            // store logo first
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('categories', 'public');
            } else {
                $imagePath = null;
            }

            // generate slug
            $slug = Str::slug($request->input('name'));

            $categorie = Category::create([
                'name' => $request->name,
                'image' => $imagePath,
                'slug' => $slug,
                'statut' => 'active'
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Catégorie enregistrée avec succès.',
                'data' => $categorie
            ], 201);

        } catch (Exception $e) {
            return response()->json($e);
        }
    }



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getCategories(Request $request)
    {
        try {
            $perPage = $request->input('perPage', 15);
            $query = $request->input('query');
            $categoryQuery = Category::where('statut', 'active');


            if ($query) {
                $categoryQuery->where('name', 'like', '%' . $query . '%');
            }

            $categoryData = $categoryQuery->paginate($perPage);

            return response()->json([
                'status' => 'success',
                'current_page' => $categoryData->currentPage(),
                'data' => $categoryData->items(),
                'nextUrl' => $categoryData->nextPageUrl(),
                'prevUrl' => $categoryData->previousPageUrl(),
                'total' => $categoryData->total(),
            ], 200); 

        } catch (Exception $e) {
            return response()->json($e);
        }
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CategoriesUpdateRequest $request, Category $category)
    {
        try {
            $categoryData = $request->only(['name', 'statut']);
            
            if (empty($categoryData) && !$request->hasFile('image')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Aucune information à mettre à jour.',
                ], 400);
            }

            if($request->hasFile('image')){
                //delete old image
                Storage::delete($category->image);

                // store new image
                $imagePath = $request->file('image')->store('categories', 'public');
                $category->update(['image' => $imagePath]);
            }

            $category->update($categoryData);

            return response()->json([
                'status' => 'success',
                'message' => 'Mise à jour de la catégorie effectuée.',
                'data' => $category,
            ], 200);

        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete(Category $category)
    {
        try {
            // solf delete
            $category->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Suppression effectuée avec succès.'
            ], 200);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }
}

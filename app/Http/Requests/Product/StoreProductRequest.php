<?php

namespace App\Http\Requests\Product;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|string|unique:products,title',
            'caracteristics' => 'required|string',
            'delivering' => 'nullable|string',
            'old_price' => 'nullable|numeric|min:0',
            'current_price' => 'required|numeric|min:0',
            'category_ids' => 'array|required_without_all:new_category',
            'category_ids.*' => 'exists:categories,id',
            'new_category' => 'nullable|string|unique:categories,name',
            'disponibility' => 'required|in:-1,0,1',
            'quantity' => 'nullable|integer|min:1',
        ];
    }


    /**
     * Determine if the user is authorized to make this request.
    */

    public function failedValidation(Validator $validator){
        throw new HttpResponseException(response()->json([
            'success' => false,
            'error' => true,
            'message' => 'Erreur de validation',
            'errorsList' => $validator->errors()
        ]));
    }
}
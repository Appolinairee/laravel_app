<?php

namespace App\Http\Requests\Product;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class UpdateProductRequest extends FormRequest
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
            'title' => 'sometimes|string|unique:products,title',
            'caracteristics' => 'sometimes|string',
            'delivering' => 'sometimes|string',
            'old_price' => 'sometimes|numeric|min:0',
            'current_price' => 'sometimes|numeric|min:0',
            'category_ids' => 'sometimes|required_without_all:new_category',
            'category_ids.*' => 'exists:categories,id',
            'new_category' => 'sometimes|string|unique:categories,name',
            'disponibility' => 'sometimes|in:-1,0,1',
            'quantity' => 'sometimes|integer|min:1',
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
        ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY));
    }
}

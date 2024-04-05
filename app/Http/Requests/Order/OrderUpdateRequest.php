<?php

namespace App\Http\Requests\Order;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class OrderUpdateRequest extends FormRequest
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
            'status' => 'sometimes|integer|in:-1, 0, 1, 2, 3, 4',
            'shipping_address' => 'sometimes|string',
            'shipping_price' => 'sometimes|numeric|min:0',
            'shipping_preview' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            'shipping_service' => 'sometimes|string',
            'shipping_date' => 'sometimes|date|date_format:Y-m-d H:i', 
            'shipping_contact'=> 'regex:/^\d{8}$/'
        ];
    }


    /**
    * Determine if the user is authorized to make this request.
    **/
    public function failedValidation(Validator $validator){
        throw new HttpResponseException(response()->json([
            'success' => false,
            'error' => true,
            'message' => 'Erreur de validation',
            'errorsList' => $validator->errors()
        ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY));
    }
}

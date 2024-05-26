<?php

namespace App\Http\Requests\User;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class StoreCreatorRequest extends FormRequest
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
            'name' => 'required|string|unique:creators,name',
            'phone' => 'required|string|unique:creators,phone',
            'email' => 'nullable|email|unique:creators,email',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:3000',
            'description' => 'nullable|string',
            'location' => 'nullable|string',
            'delivery_options' => 'nullable|string',
            'payment_options' => 'nullable|string',
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
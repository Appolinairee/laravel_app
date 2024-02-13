<?php

namespace App\Http\Requests\User;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateCreatorRequest extends FormRequest
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
            'name' => 'sometimes|required|unique:creators,name',
            'phone' => 'sometimes|string|unique:creators,phone',
            'email' => 'sometimes|email|unique:creators,email',
            'logo' => 'sometimes|image|mimes:jpeg,png,jpg|max:5120',
            'description' => 'sometimes|string',
            'location' => 'sometimes|string',
            'delivery_poptions' => 'sometimes|string',
            'payment_options' => 'sometimes|string',
        ];
    }

    public function failedValidation(Validator $validator){
        throw new HttpResponseException(response()->json([
            'success' => false,
            'error' => true,
            'message' => 'Erreur de validation',
            'errorsList' => $validator->errors()
        ]));
    }
}

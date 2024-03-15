<?php

namespace App\Http\Requests\Auth;

use App\Rules\notInTrash;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;


class RegisterRequest extends FormRequest{
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
            'name' => 'required|unique:users,name',
            'email' => ['required', 'email', new notInTrash, 'unique:users,email'],
            'password' => 'required|min:8|confirmed',
            'phone' => 'nullable|digits_between:8,20',
            'location' => 'nullable', 
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
